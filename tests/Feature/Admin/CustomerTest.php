<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
        $this->withoutVite();
    }

    public function test_guest_cannot_access_admin_customer_page()
    {
        $response = $this->get(route('admin.view.customer'));

        $response->assertRedirect(route('view.login'));
    }

    public function test_regular_user_cannot_access_admin_customer_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'user')
            ->get(route('admin.view.customer'));

        $response->assertRedirect(route('view.login'));
    }

    public function test_admin_can_view_customer_page_without_search()
    {
        $this->actingAs($this->admin, 'admin');

        User::factory()->count(5)->create();

        $response = $this->get(route('admin.view.customer'));

        $response->assertOk();
        $response->assertViewIs('admin.customer.index');
        $response->assertViewHas('users');
        $response->assertViewHas('search', '');
    }

    public function test_admin_can_search_customer_by_name()
    {
        $this->actingAs($this->admin, 'admin');

        User::factory()->create(['name' => 'Budi Santoso']);
        User::factory()->create(['name' => 'Ani Wijaya']);

        $response = $this->get(route('admin.view.customer', ['search' => 'Budi']));

        $response->assertOk();
        $response->assertViewIs('admin.customer.index');
        $response->assertViewHas('search', 'Budi');

        $users = $response->viewData('users');
        $this->assertTrue($users->contains('name', 'Budi Santoso'));
        $this->assertFalse($users->contains('name', 'Ani Wijaya'));
    }

    public function test_admin_can_reset_customer_password()
    {
        $this->actingAs($this->admin, 'admin');

        $customer = User::factory()->create([
            'email' => 'pelanggan@example.com',
            'password' => Hash::make('passwordlama'),
        ]);

        $response = $this->patch(route('admin.reset.customer.password', $customer->id));

        $response->assertRedirect(route('admin.view.customer'));
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('pelanggan@example.com', $customer->fresh()->password));
    }

    public function test_reset_customer_password_with_invalid_id()
    {
        $invalidUserId = 999;

        $response = $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.reset.customer.password', $invalidUserId));

        $response->assertNotFound();
    }
}
