<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Admin::factory()->create([
            'email' => 'admin@test.com',
        ]);

        $this->withoutVite();
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $response = $this->post(route('admin.login'), [
            'email' => 'admin@test.com',
            'password' => 'halo1234',
        ]);

        $response->assertRedirectToRoute('admin.view.home');
        $this->assertAuthenticated('admin');
    }

    public function test_user_cannot_login_with_incorrect_password()
    {
        $response = $this->post(route('admin.login'), [
            'email' => 'admin@test.com',
            'password' => 'password-salah',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $response->assertSessionHasInput('email');
        $this->assertGuest('admin');
    }

    public function test_user_cannot_login_with_non_existent_email()
    {
        $response = $this->from(route('view.login'))->post(route('admin.login'), [
            'email' => 'tidakada@test.com',
            'password' => 'halo1234',
        ]);

        $response->assertRedirectToRoute('view.login');
        $response->assertSessionHas('error');
        $this->assertGuest('admin');
    }

    public function test_email_is_required_for_login()
    {
        $response = $this->from(route('view.login'))->post(route('admin.login'), [
            'email' => '',
            'password' => 'halo1234',
        ]);

        $response->assertRedirectToRoute('view.login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_password_is_required_for_login()
    {
        $response = $this->from(route('view.login'))->post(route('admin.login'), [
            'email' => 'admin@test.com',
            'password' => '',
        ]);

        $response->assertRedirectToRoute('view.login');
        $response->assertSessionHasErrors('password');
        $this->assertGuest('admin');
    }

    public function test_email_must_be_a_valid_email_address()
    {
        $response = $this->from(route('view.login'))->post(route('admin.login'), [
            'email' => 'ini-bukan-email',
            'password' => 'halo1234',
        ]);

        $response->assertRedirectToRoute('view.login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }
}
