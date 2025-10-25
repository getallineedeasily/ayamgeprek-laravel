<?php

namespace Tests\Feature\User;

use App\Models\Food;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HistoryTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected function setUp(): void
    {
        parent::setUp();
        Food::factory()->create();
        $this->user = User::factory()->create();
        Storage::fake('local');
        $this->withoutVite();
    }

    public function test_guest_cannot_view_history_page()
    {
        $response = $this->get(route('user.view.history'));

        $response->assertRedirect(route('view.login'));
    }

    public function test_user_can_view_history_page_with_no_transactions()
    {
        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.view.history'));

        $response->assertOk();
        $response->assertViewIs('users.history.index');
        $response->assertViewHas('transaction', function ($transactions) {
            return $transactions->isEmpty();
        });
    }

    public function test_user_sees_their_own_paginated_and_ordered_transactions()
    {
        $otherUser = User::factory()->create();

        Transaction::factory()->create([
            'user_id' => $otherUser->id,
            'invoice_id' => 'INV-999',
            'total' => 1000,
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'invoice_id' => 'INV-101',
            'address' => 'jakarta',
            'total' => 50,
            'created_at' => now()->subDays(3),
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'invoice_id' => 'INV-101',
            'address' => 'jakarta',
            'total' => 100,
            'created_at' => now()->subDays(3),
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'invoice_id' => 'INV-102',
            'total' => 200,
            'created_at' => now()->subDays(1),
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'invoice_id' => 'INV-103',
            'total' => 300,
            'created_at' => now()->subDays(2),
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'invoice_id' => 'INV-104',
            'total' => 400,
            'created_at' => now()->subDays(4),
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.view.history'));

        $response->assertOk();
        $response->assertViewIs('users.history.index');
        $response->assertDontSeeText('INV-999');

        $paginator = $response->viewData('transaction');
        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertCount(3, $paginator->items());
        $this->assertEquals(4, $paginator->total());

        $items = $paginator->items();
        $this->assertEquals('INV-102', $items[0]->invoice_id);
        $this->assertEquals('INV-103', $items[1]->invoice_id);
        $this->assertEquals('INV-101', $items[2]->invoice_id);
        $this->assertEquals(150, $items[2]->total);
    }

    public function test_guest_cannot_view_history_detail()
    {
        $transaction = Transaction::factory()->create();

        $response = $this->get(route('user.view.history.detail', $transaction));

        $response->assertRedirect(route('view.login'));
    }

    public function test_user_cannot_view_another_users_history_detail()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $transactionForUserA = Transaction::factory()->create([
            'user_id' => $userA->id,
        ]);

        $response = $this->actingAs($userB, 'user')
            ->get(route('user.view.history.detail', $transactionForUserA));

        $response->assertNotFound();
    }

    public function test_user_can_view_their_own_history_detail()
    {
        $food = Food::factory()->create();

        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'invoice_id' => 'INV123',
            'food_id' => $food->id,
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.view.history.detail', $transaction));

        $response->assertOk();
        $response->assertViewIs('users.history.detail');
    }

    public function test_history_detail_view_contains_correct_items_with_food()
    {
        $food1 = Food::factory()->create();
        $food2 = Food::factory()->create();

        $t1 = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'invoice_id' => 'INVABC',
            'food_id' => $food1->id,
        ]);

        $t2 = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'invoice_id' => 'INVABC',
            'food_id' => $food2->id,
        ]);

        $otherTransaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'invoice_id' => 'INVXYZ',
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.view.history.detail', $t1));

        $response->assertOk();
        $response->assertViewHas('transactions', function ($viewTransactions) use ($t1, $t2, $otherTransaction) {
            $containsT1 = $viewTransactions->contains('id', $t1->id);
            $containsT2 = $viewTransactions->contains('id', $t2->id);
            $notContainsOther = !$viewTransactions->contains('id', $otherTransaction->id);
            $foodRelationLoaded = $viewTransactions->first()->relationLoaded('food');

            return $containsT1 && $containsT2 && $notContainsOther && $foodRelationLoaded;
        });
    }

    public function test_guest_cannot_view_payment_proof()
    {
        $transaction = Transaction::factory()->create(['user_id' => $this->user->id]);

        $response = $this->get(route('user.view.payment.proof', $transaction));

        $response->assertRedirect(route('view.login'));
    }

    public function test_user_cannot_view_another_users_payment_proof()
    {
        $otherUser = User::factory()->create();
        $otherTransaction = Transaction::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.view.payment.proof', $otherTransaction));

        $response->assertNotFound();
    }

    public function test_user_can_view_own_payment_proof_if_file_exists()
    {
        $fileName = 'my_proof.jpg';
        $fakeFile = UploadedFile::fake()->image($fileName);
        Storage::disk('local')->putFileAs('/payment_proof', $fakeFile, $fileName);

        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'payment_proof' => $fileName,
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.view.payment.proof', $transaction));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/jpeg');
    }

    public function test_user_gets_404_if_payment_proof_file_is_missing_from_storage()
    {
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'payment_proof' => 'file_yang_tidak_ada.jpg',
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.view.payment.proof', $transaction));

        $response->assertNotFound();
    }

    public function test_user_gets_404_if_transaction_has_no_proof_filename()
    {
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'payment_proof' => null,
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.view.payment.proof', $transaction));

        $response->assertNotFound();
    }
}
