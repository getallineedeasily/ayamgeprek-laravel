<?php

namespace Tests\Feature\Admin;

use App\Enums\TransactionStatus;
use App\Models\Admin;
use App\Models\Food;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create(['name' => 'Admin Utama']);
        Storage::fake('local');
        Food::factory()->create();
        User::factory()->create();
        $this->withoutVite();
    }

    public function test_guest_cannot_view_admin_transactions()
    {
        $response = $this->get(route('admin.view.txn'));
        $response->assertRedirect(route('view.login'));
    }

    public function test_user_cannot_view_admin_transactions()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'user')
            ->get(route('admin.view.txn'));
        $response->assertRedirect(route('view.login'));
    }

    public function test_admin_can_view_transactions_page_with_defaults()
    {
        Transaction::factory()->count(5)->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.view.txn'));

        $response->assertOk();
        $response->assertViewIs('admin.transaction.index');
        $response->assertViewHas('transactions', function ($transactions) {
            return $transactions->total() === 5;
        });
        $response->assertViewHas('statuses', TransactionStatus::cases());
        $response->assertViewHasAll(['search' => '', 'status' => '', 'start_date' => '', 'end_date' => '']);
    }

    public function test_admin_can_filter_transactions_by_status_and_date()
    {
        Transaction::factory()->create([
            'status' => TransactionStatus::PENDING_PAYMENT->value,
            'created_at' => now()->subDay()
        ]);
        Transaction::factory()->create([
            'status' => TransactionStatus::DELIVERED->value,
            'created_at' => now()->subDay()
        ]);
        Transaction::factory()->create([
            'status' => TransactionStatus::CONFIRMED->value,
            'created_at' => now()->subMonth()
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.view.txn', [
                'search' => '',
                'status' => TransactionStatus::PENDING_PAYMENT->value,
                'start_date' => now()->subDays(2)->toDateString(),
                'end_date' => now()->toDateString(),
            ]));

        $response->assertOk();
        $response->assertViewHas('transactions', function ($transactions) {
            return $transactions->total() === 1;
        });
        $response->assertViewHas('status', TransactionStatus::PENDING_PAYMENT->value);
    }

    public function test_admin_gets_validation_error_for_invalid_status_enum()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.view.txn'))
            ->get(route('admin.view.txn', ['status' => 'INVALID_STATUS']));

        $response->assertRedirect(route('admin.view.txn'));
        $response->assertSessionHasErrors('status');
    }

    public function test_admin_gets_validation_error_for_invalid_date_format()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.view.txn'))
            ->get(route('admin.view.txn', ['start_date' => 'bukan-tanggal-valid']));

        $response->assertRedirect(route('admin.view.txn'));
        $response->assertSessionHasErrors('start_date');
    }

    public function test_guest_cannot_view_payment_proof()
    {
        $transaction = Transaction::factory()->create();
        $response = $this->get(route('admin.view.payment.proof', $transaction));
        $response->assertRedirect(route('view.login'));
    }

    public function test_user_cannot_view_admin_payment_proof()
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create();

        $response = $this->actingAs($user, 'user')
            ->get(route('admin.view.payment.proof', $transaction));
        $response->assertRedirect(route('view.login'));
    }

    public function test_admin_can_view_payment_proof_if_file_exists()
    {
        $fileName = 'proof_file.jpg';
        $fakeFile = UploadedFile::fake()->image($fileName);
        Storage::disk('local')->putFileAs('/payment_proof', $fakeFile, $fileName);

        $transaction = Transaction::factory()->create(['payment_proof' => $fileName]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.view.payment.proof', $transaction));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/jpeg');
    }

    public function test_admin_gets_404_if_payment_proof_file_is_missing()
    {
        $transaction = Transaction::factory()->create(['payment_proof' => 'missing_file.jpg']);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.view.payment.proof', $transaction));

        $response->assertNotFound();
    }

    public function test_admin_gets_404_if_transaction_has_no_proof_filename()
    {
        $transaction = Transaction::factory()->create(['payment_proof' => null]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.view.payment.proof', $transaction));

        $response->assertNotFound();
    }

    public function test_guest_cannot_view_edit_transaction_page()
    {
        $transaction = Transaction::factory()->create();
        $response = $this->get(route('admin.edit.txn', $transaction));
        $response->assertRedirect(route('view.login'));
    }

    public function test_user_cannot_view_edit_transaction_page()
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create();
        $response = $this->actingAs($user, 'user')
            ->get(route('admin.edit.txn', $transaction));
        $response->assertRedirect(route('view.login'));
    }

    public function test_admin_can_view_edit_transaction_page_with_correct_data()
    {
        $user = User::factory()->create();
        $food = Food::factory()->create();

        $t1 = Transaction::factory()->create([
            'invoice_id' => 'INVEDIT123',
            'user_id' => $user->id,
            'food_id' => $food->id,
        ]);

        $t2 = Transaction::factory()->create([
            'invoice_id' => 'INVEDIT123',
            'user_id' => $user->id,
            'food_id' => $food->id,
        ]);

        $otherTransaction = Transaction::factory()->create([
            'invoice_id' => 'INVXYZ999',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.edit.txn', $t1));

        $response->assertOk();
        $response->assertViewIs('admin.transaction.edit');

        $response->assertViewHas('transactions', function ($viewTransactions) use ($t1, $t2, $otherTransaction) {
            $hasT1 = $viewTransactions->contains('id', $t1->id);
            $hasT2 = $viewTransactions->contains('id', $t2->id);
            $hasNoOther = !$viewTransactions->contains('id', $otherTransaction->id);
            $relationsLoaded = $viewTransactions->first()->relationLoaded('food') &&
                $viewTransactions->first()->relationLoaded('user');

            return $hasT1 && $hasT2 && $hasNoOther && $relationsLoaded;
        });

        $response->assertViewHas('statuses', function ($viewStatuses) {
            $hasPending = $viewStatuses->contains(TransactionStatus::PENDING_PAYMENT);
            $hasCancelled = $viewStatuses->contains(TransactionStatus::CANCELLED);

            return $hasPending && !$hasCancelled;
        });
    }

    public function test_guest_cannot_update_transaction()
    {
        $transaction = Transaction::factory()->create();
        $response = $this->patch(route('admin.edit.txn', $transaction), [
            'status' => TransactionStatus::CONFIRMED->value
        ]);
        $response->assertRedirect(route('view.login'));
    }

    public function test_user_cannot_update_transaction()
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create();
        $response = $this->actingAs($user, 'user')
            ->patch(route('admin.edit.txn', $transaction), [
                'status' => TransactionStatus::CONFIRMED->value
            ]);
        $response->assertRedirect(route('view.login'));
    }

    public function test_admin_cannot_update_if_status_is_pending_payment_due_to_gate()
    {
        $transaction = Transaction::factory()->create([
            'status' => TransactionStatus::PENDING_PAYMENT->value
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.edit.txn', $transaction), [
                'status' => TransactionStatus::DELIVERED->value
            ]);

        $response->assertNotFound();
    }

    public function test_admin_cannot_update_if_status_is_cancelled_due_to_gate()
    {
        $transaction = Transaction::factory()->create([
            'status' => TransactionStatus::CANCELLED->value
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.edit.txn', $transaction), [
                'status' => TransactionStatus::CANCELLED->value
            ]);

        $response->assertNotFound();
    }

    public function test_admin_can_update_valid_transaction_status()
    {
        $t1 = Transaction::factory()->create([
            'invoice_id' => 'INVUPDATE1',
            'status' => TransactionStatus::WAITING_CONFIRMATION->value
        ]);
        $t2 = Transaction::factory()->create([
            'invoice_id' => 'INVUPDATE1',
            'status' => TransactionStatus::WAITING_CONFIRMATION->value
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.edit.txn', $t1), [
                'status' => TransactionStatus::CONFIRMED->value
            ]);

        $response->assertRedirect(route('admin.edit.txn', ['transaction' => $t1->invoice_id]));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('transactions', ['id' => $t1->id, 'status' => TransactionStatus::CONFIRMED->value]);
        $this->assertDatabaseHas('transactions', ['id' => $t2->id, 'status' => TransactionStatus::CONFIRMED->value]);
    }

    public function test_admin_update_to_pending_payment_deletes_proof_and_nulls_field()
    {
        $fileName = 'payment_proof.jpg';
        Storage::disk('local')->putFileAs('/payment_proof/', UploadedFile::fake()->image('fake'), $fileName);

        $t1 = Transaction::factory()->create([
            'invoice_id' => 'INVUPDATE2',
            'status' => TransactionStatus::WAITING_CONFIRMATION->value,
            'payment_proof' => $fileName
        ]);
        $t2 = Transaction::factory()->create([
            'invoice_id' => 'INVUPDATE2',
            'status' => TransactionStatus::WAITING_CONFIRMATION->value,
            'payment_proof' => $fileName
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.edit.txn', $t1), [
                'status' => TransactionStatus::PENDING_PAYMENT->value
            ]);

        $response->assertRedirect(route('admin.edit.txn', ['transaction' => $t1->invoice_id]));
        $this->assertFileDoesNotExist(Storage::disk('local')->path('/payment_proof/' . $fileName));
        $this->assertDatabaseHas('transactions', ['id' => $t1->id, 'payment_proof' => null]);
        $this->assertDatabaseHas('transactions', ['id' => $t2->id, 'payment_proof' => null]);
    }

    public function test_admin_gets_validation_error_for_invalid_status()
    {
        $transaction = Transaction::factory()->create([
            'status' => TransactionStatus::WAITING_CONFIRMATION->value
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.edit.txn', ['transaction' => $transaction->invoice_id]))
            ->patch(route('admin.update.txn', $transaction), [
                'status' => 'STATUS_PALSU'
            ]);

        $response->assertRedirect(route('admin.edit.txn', ['transaction' => $transaction->invoice_id]));
        $response->assertSessionHasErrors('status');
    }
}
