<?php

namespace Tests\Feature\Admin;

use App\Enums\TransactionStatus;
use App\Models\Admin;
use App\Models\Food;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
        Food::factory()->create();
        User::factory()->create();
        $this->withoutVite();
    }

    public function test_guest_cannot_view_report_page()
    {
        $response = $this->get(route('admin.view.report'));

        $response->assertRedirect(route('view.login'));
    }

    public function test_regular_user_cannot_view_report_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'user')
            ->get(route('admin.view.report'));

        $response->assertRedirect(route('view.login'));
    }

    public function test_admin_can_view_report_page()
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.view.report'));

        $response->assertOk();
        $response->assertViewIs('admin.report.index');
    }

    public function test_guest_cannot_print_report_page()
    {
        $response = $this->get(route('admin.print.report'));

        $response->assertRedirect(route('view.login'));
    }

    public function test_regular_user_cannot_print_report_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'user')
            ->get(route('admin.print.report'));

        $response->assertRedirect(route('view.login'));
    }

    public function test_admin_can_generate_print_report_with_valid_dates()
    {
        $this->actingAs($this->admin, 'admin');

        $start = Carbon::now()->subDays(3)->toDateString();
        $end = Carbon::now()->toDateString();

        Transaction::factory()->create([
            'created_at' => Carbon::now()->subDays(2),
            'status' => TransactionStatus::DELIVERED->value,
            'total' => 10000,
        ]);

        Transaction::factory()->create([
            'created_at' => Carbon::now()->subDays(10),
            'status' => TransactionStatus::DELIVERED->value,
            'total' => 9999,
        ]);

        Transaction::factory()->create([
            'created_at' => Carbon::now()->subDays(1),
            'status' => TransactionStatus::PENDING_PAYMENT->value,
            'total' => 8888,
        ]);

        $response = $this->get(route('admin.print.report', [
            'start_date' => $start,
            'end_date' => $end,
        ]));

        $response->assertOk();
        $response->assertViewIs('admin.report.print');
        $response->assertViewHasAll([
            'start_date',
            'end_date',
            'statuses',
            'transactions',
            'totalSales',
            'totalRevenue',
            'mostSoldFood',
            'totalCustomer',
            'now',
        ]);
    }

    public function test_print_report_requires_valid_dates()
    {
        $this->actingAs($this->admin, 'admin');

        $response = $this->from(route('admin.view.report'))
            ->get(route('admin.print.report', [
                'start_date' => 'invalid-date',
                'end_date' => '',
            ]));

        $response->assertRedirectToRoute('admin.view.report');
        $response->assertSessionHasErrors(['start_date', 'end_date']);
    }
}
