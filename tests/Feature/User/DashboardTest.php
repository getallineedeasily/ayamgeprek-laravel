<?php

namespace Tests\Feature\User;

use App\Enums\TransactionStatus;
use App\Models\Food;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Food::factory()->create();

        $this->withoutVite();

    }

    public function test_guest_cannot_access_dashboard()
    {
        $response = $this->get(route('user.view.home'));

        $response->assertRedirect(route('view.login'));
    }

    public function test_user_sees_dashboard_with_correct_name()
    {
        $user = User::factory()->create(['name' => 'Andi']);

        $response = $this->actingAs($user, 'user')
            ->get(route('user.view.home'));

        $response->assertOk();
        $response->assertViewIs('users.dashboard.index');
        $response->assertViewHas('userName', 'Andi');
    }

    public function test_user_with_no_active_orders_sees_zero_count()
    {
        $user = User::factory()->create();

        Transaction::factory()->create([
            'user_id' => $user->id,
            'invoice_id' => 'INV-001',
            'status' => TransactionStatus::DELIVERED->value,
        ]);

        Transaction::factory()->create([
            'user_id' => $user->id,
            'invoice_id' => 'INV-002',
            'status' => TransactionStatus::CANCELLED->value,
        ]);

        $response = $this->actingAs($user, 'user')
            ->get(route('user.view.home'));

        $response->assertOk();
        $response->assertViewHas('hasActiveOrder', false);
        $response->assertViewHas('activeOrderCount', 0);
    }

    public function test_user_with_active_orders_sees_correct_count()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Transaction::factory()->create([
            'user_id' => $user->id,
            'invoice_id' => 'INV-101',
            'status' => TransactionStatus::PENDING_PAYMENT->value,
        ]);

        Transaction::factory()->create([
            'user_id' => $user->id,
            'invoice_id' => 'INV-102',
            'status' => TransactionStatus::WAITING_CONFIRMATION->value,
        ]);

        Transaction::factory()->create([
            'user_id' => $user->id,
            'invoice_id' => 'INV-102',
            'status' => TransactionStatus::WAITING_CONFIRMATION->value,
        ]);

        Transaction::factory()->create([
            'user_id' => $user->id,
            'invoice_id' => 'INV-103',
            'status' => TransactionStatus::CANCELLED->value,
        ]);

        Transaction::factory()->create([
            'user_id' => $otherUser->id,
            'invoice_id' => 'INV-999',
            'status' => TransactionStatus::WAITING_CONFIRMATION->value,
        ]);

        $response = $this->actingAs($user, 'user')
            ->get(route('user.view.home'));

        $response->assertOk();
        $response->assertViewHas('hasActiveOrder', true);
        $response->assertViewHas('activeOrderCount', 2);
    }
}
