<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Food;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        User::factory()->create();
        Food::factory()->create();
        $this->admin = Admin::factory()->create(['name' => 'Admin Utama']);
        $this->withoutVite();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_guest_cannot_access_admin_dashboard()
    {
        $response = $this->get(route('admin.view.home'));

        $response->assertRedirect(route('view.login'));
    }

    public function test_regular_user_cannot_access_admin_dashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'user')
            ->get(route('admin.view.home'));

        $response->assertRedirect(route('view.login'));
    }

    public function test_admin_can_view_dashboard_with_default_today_filter()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.view.home'));

        $response->assertOk();
        $response->assertViewIs('admin.dashboard.index');
        $response->assertViewHas('name', 'Admin Utama');
        $response->assertViewHas('filter', '');
        $response->assertViewHas('totalRevenue');
        $response->assertViewHas('totalSales');
        $response->assertViewHas('mostSoldFood');
        $response->assertViewHas('totalCustomer');
    }

    public function test_admin_can_view_dashboard_with_month_filter()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.view.home', ['filter' => 'month']));

        $response->assertOk();
        $response->assertViewIs('admin.dashboard.index');
        $response->assertViewHas('name', 'Admin Utama');
        $response->assertViewHas('filter', 'month');
        $response->assertViewHas('totalRevenue');
        $response->assertViewHas('totalSales');
        $response->assertViewHas('mostSoldFood');
        $response->assertViewHas('totalCustomer');
    }

    public function test_admin_gets_validation_error_for_invalid_filter()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.view.home'))
            ->get(route('admin.view.home', ['filter' => 'invalid_value']));

        $response->assertRedirect(route('admin.view.home'));
        $response->assertSessionHasErrors('filter');
    }
}
