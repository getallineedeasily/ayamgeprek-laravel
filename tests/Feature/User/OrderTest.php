<?php

namespace Tests\Feature\User;

use App\Enums\TransactionStatus;
use App\Models\Food;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Str;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $transaction;
    protected $food1;
    protected $food2;

    protected $statuses = [
        TransactionStatus::WAITING_CONFIRMATION->value,
        TransactionStatus::CONFIRMED->value,
        TransactionStatus::DELIVERED->value,
        TransactionStatus::CANCELLED->value,
    ];

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->user = User::factory()->create();
        $this->food1 = Food::factory()->create(['price' => 10000]);
        $this->food2 = Food::factory()->create(['price' => 5000]);
        $this->transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'invoice_id' => 'INV123',
            'status' => TransactionStatus::PENDING_PAYMENT->value,
            'payment_proof' => null,
        ]);

        $this->withoutVite();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(null);
        parent::tearDown();
    }

    public function test_guest_cannot_view_order_page()
    {
        $response = $this->get(route('user.view.order'));

        $response->assertRedirect(route('view.login'));
    }

    public function test_authenticated_user_can_view_order_page_with_all_foods()
    {
        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.view.order'));

        $response->assertOk();
        $response->assertViewIs('users.order.index');

        $response->assertViewHas('foods', function ($viewFoods) {
            return $viewFoods->count() === 2;
        });
    }

    public function test_guest_cannot_create_order()
    {
        $response = $this->post(route('user.create.txn'), [
            'order' => [
                ['food_id' => $this->food1->id, 'quantity' => 1]
            ]
        ]);

        $response->assertRedirect(route('view.login'));
    }

    public function test_user_can_create_order_successfully()
    {
        $payload = [
            'order' => [
                ['food_id' => $this->food1->id, 'quantity' => 2],
                ['food_id' => $this->food2->id, 'quantity' => 1],
            ]
        ];

        $userA = User::factory()->create();

        $response = $this->actingAs($userA, 'user')
            ->post(route('user.create.txn'), $payload);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $userA->id,
            'address' => $userA->address,
            'food_id' => $this->food1->id,
            'quantity' => 2,
            'total' => 20000,
            'status' => TransactionStatus::PENDING_PAYMENT->value,
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $userA->id,
            'address' => $userA->address,
            'food_id' => $this->food2->id,
            'quantity' => 1,
            'total' => 5000,
            'status' => TransactionStatus::PENDING_PAYMENT->value,
        ]);

        $t1 = Transaction::where('user_id', '=', $userA->id)->get();

        $response->assertRedirect(route('user.view.history.detail', ['transaction' => $t1[0]->invoice_id]));
        $response->assertSessionHas('success');
    }

    public function test_order_creation_filters_out_null_quantity_items()
    {
        $payload = [
            'order' => [
                ['food_id' => $this->food1->id, 'quantity' => 3],
                ['food_id' => $this->food2->id, 'quantity' => null],
            ]
        ];

        $userA = User::factory()->create();

        $this->actingAs($userA, 'user')
            ->post(route('user.create.txn'), $payload);

        $this->assertDatabaseHas('transactions', ['food_id' => $this->food1->id]);
        $this->assertDatabaseMissing('transactions', ['food_id' => $this->food2->id]);
    }

    public function test_order_is_required()
    {
        $response = $this->actingAs($this->user, 'user')
            ->from(route('user.view.order'))
            ->post(route('user.create.txn'), ['order' => []]);

        $response->assertRedirect(route('user.view.order'));
        $response->assertSessionHasErrors('order');
    }

    public function test_food_id_must_exist()
    {
        $payload = [
            'order' => [
                ['food_id' => 999, 'quantity' => 1]
            ]
        ];

        $response = $this->actingAs($this->user, 'user')
            ->from(route('user.view.order'))
            ->post(route('user.create.txn'), $payload);

        $response->assertRedirect(route('user.view.order'));
        $response->assertSessionHasErrors('order.0.food_id');
    }

    public function test_quantity_must_be_at_least_1()
    {
        $payload = [
            'order' => [
                ['food_id' => $this->food1->id, 'quantity' => 0]
            ]
        ];

        $response = $this->actingAs($this->user, 'user')
            ->from(route('user.view.order'))
            ->post(route('user.create.txn'), $payload);

        $response->assertRedirect(route('user.view.order'));
        $response->assertSessionHasErrors('order.0.quantity');
    }

    public function test_guest_cannot_upload_proof()
    {
        $response = $this->patch(route('user.upload.payment.proof', $this->transaction), [
            'payment_proof' => UploadedFile::fake()->image('proof.jpg')
        ]);

        $response->assertRedirect(route('view.login'));
    }

    public function test_user_cannot_upload_proof_for_another_users_transaction()
    {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser, 'user')
            ->patch(route('user.upload.payment.proof', $this->transaction), [
                'payment_proof' => UploadedFile::fake()->image('proof.jpg')
            ]);

        $response->assertNotFound();
    }

    public function test_payment_proof_is_required()
    {
        $response = $this->actingAs($this->user, 'user')
            ->patch(route('user.upload.payment.proof', $this->transaction), [
                'payment_proof' => null
            ]);

        $response->assertSessionHasErrors('payment_proof');
    }

    public function test_payment_proof_must_be_an_image()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->user, 'user')
            ->patch(route('user.upload.payment.proof', $this->transaction), [
                'payment_proof' => $file
            ]);

        $response->assertSessionHasErrors('payment_proof');
    }

    public function test_payment_proof_must_be_under_2mb()
    {
        $file = UploadedFile::fake()->image('large_proof.jpg')->size(3000);

        $response = $this->actingAs($this->user, 'user')
            ->patch(route('user.upload.payment.proof', $this->transaction), [
                'payment_proof' => $file
            ]);

        $response->assertSessionHasErrors('payment_proof');
    }

    public function test_user_can_successfully_upload_payment_proof()
    {
        $file = UploadedFile::fake()->image('payment.jpg');

        $response = $this->actingAs($this->user, 'user')
            ->patch(route('user.upload.payment.proof', $this->transaction), [
                'payment_proof' => $file
            ]);

        $response->assertRedirect(route('user.view.history.detail', ['transaction' => $this->transaction->invoice_id]));
        $response->assertSessionHas('success');

        $this->transaction->refresh();
        Storage::disk('local')->assertExists('/payment_proof/' . $this->transaction->payment_proof);
        $this->assertEquals(TransactionStatus::WAITING_CONFIRMATION->value, $this->transaction->status);
        $this->assertNotNull($this->transaction->payment_proof);
    }

    public function test_upload_updates_all_transactions_with_same_invoice_id()
    {
        $transaction2 = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'invoice_id' => $this->transaction->invoice_id,
            'status' => TransactionStatus::PENDING_PAYMENT->value,
        ]);

        $file = UploadedFile::fake()->image('payment.jpg');

        $this->actingAs($this->user, 'user')
            ->patch(route('user.upload.payment.proof', $this->transaction), [
                'payment_proof' => $file
            ]);

        $this->transaction->refresh();
        $transaction2->refresh();

        $this->assertEquals(TransactionStatus::WAITING_CONFIRMATION->value, $this->transaction->status);
        $this->assertEquals(TransactionStatus::WAITING_CONFIRMATION->value, $transaction2->status);
        $this->assertEquals($this->transaction->payment_proof, $transaction2->payment_proof);
    }

    public function test_user_cannot_upload_proof_if_status_is_not_pending_payment()
    {
        Carbon::setTestNow(Carbon::create(2025, 10, 01, 10, 0, 0));
        $file = UploadedFile::fake()->image('new_payment.jpg');
        $expectedFileName = $this->user->id . '_' . Carbon::now()->timestamp . '.' . $file->extension();

        foreach ($this->statuses as $status) {
            $this->transaction->update(['status' => $status, 'payment_proof' => 'bukti.jpg']);

            $response = $this->actingAs($this->user, 'user')
                ->patch(route('user.upload.payment.proof', $this->transaction), [
                    'payment_proof' => $file
                ]);

            $this->transaction->refresh();

            $response->assertNotFound();
            $this->assertNotEquals($expectedFileName, $this->transaction->payment_proof);
            Storage::disk('local')->assertMissing('/payment_proof/' . $expectedFileName);
            $this->assertEquals($status, $this->transaction->status);
        }
    }

    public function test_guest_cannot_cancel_order()
    {
        $transaction = Transaction::factory()->create();

        $response = $this->patch(route('user.cancel.order', $transaction));

        $response->assertRedirect(route('view.login'));
    }

    public function test_user_cannot_cancel_another_users_order()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $transactionA = Transaction::factory()->create([
            'user_id' => $userA->id,
            'status' => TransactionStatus::PENDING_PAYMENT->value,
        ]);

        $response = $this->actingAs($userB, 'user')
            ->patch(route('user.cancel.order', $transactionA));

        $response->assertNotFound();
        $transactionA->refresh();
        $this->assertEquals(TransactionStatus::PENDING_PAYMENT->value, $transactionA->status);
    }

    public function test_user_can_cancel_own_pending_payment_order()
    {
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'invoice_id' => 'INV123',
            'status' => TransactionStatus::PENDING_PAYMENT->value,
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->patch(route('user.cancel.order', $transaction));

        $response->assertRedirect(route('user.view.history.detail', ['transaction' => $transaction->invoice_id]));
        $response->assertSessionHas('success');

        $transaction->refresh();
        $this->assertEquals(TransactionStatus::CANCELLED->value, $transaction->status);
    }

    public function test_user_cannot_cancel_order_if_status_is_not_pending_payment()
    {
        foreach ($this->statuses as $status) {
            $transaction = Transaction::factory()->create([
                'user_id' => $this->user->id,
                'invoice_id' => 'INV456',
                'status' => $status,
            ]);

            $this->actingAs($this->user, 'user')
                ->patch(route('user.cancel.order', $transaction));

            $transaction->refresh();
            $this->assertEquals($status, $transaction->status);
        }
    }

    public function test_cancelling_order_updates_all_items_with_same_invoice_id()
    {
        $t1 = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'invoice_id' => 'INV789',
            'status' => TransactionStatus::PENDING_PAYMENT->value,
        ]);

        $t2 = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'invoice_id' => 'INV789',
            'status' => TransactionStatus::PENDING_PAYMENT->value,
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->patch(route('user.cancel.order', $t1));

        $t1->refresh();
        $t2->refresh();

        $response->assertRedirectToRoute('user.view.history.detail', ['transaction' => $t1]);
        $this->assertEquals(TransactionStatus::CANCELLED->value, $t1->status);
        $this->assertEquals(TransactionStatus::CANCELLED->value, $t2->status);
    }
}

