<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SignupTest extends TestCase
{
    use RefreshDatabase;

    private function getValidRegistrationData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Budi Santoso',
            'email' => 'budi@test.com',
            'phone' => '081234567890',
            'password' => 'passwordyangaman123',
            'address' => 'Jalan Pahlawan No. 10',
        ], $overrides);
    }

    public function test_user_can_register_successfully()
    {
        $data = $this->getValidRegistrationData();

        $response = $this->post(route('user.signup'), $data);

        $response->assertRedirect(route('user.view.home'));

        $this->assertDatabaseHas('users', [
            'email' => 'budi@test.com',
            'name' => 'Budi Santoso',
        ]);

        $user = User::first();
        $this->assertAuthenticatedAs($user, 'user');
    }

    public function test_registration_fails_if_email_is_missing()
    {
        $data = $this->getValidRegistrationData(['email' => '']);

        $response = $this->from(route('view.signup'))
            ->post(route('user.signup'), $data);

        $response->assertRedirect(route('view.signup'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest('user');
    }

    public function test_registration_fails_if_email_is_already_taken()
    {
        User::factory()->create(['email' => 'budi@test.com']);

        $data = $this->getValidRegistrationData(['email' => 'budi@test.com']);

        $response = $this->from(route('view.signup'))
            ->post(route('user.signup'), $data);

        $response->assertRedirect(route('view.signup'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest('user');
    }

    public function test_registration_fails_if_password_is_too_short()
    {
        $data = $this->getValidRegistrationData(['password' => '1234']);

        $response = $this->from(route('view.signup'))
            ->post(route('user.signup'), $data);

        $response->assertRedirect(route('view.signup'));
        $response->assertSessionHasErrors('password');
        $this->assertGuest('user');
    }

    public function test_registration_fails_if_phone_is_not_numeric()
    {
        $data = $this->getValidRegistrationData(['phone' => 'inipasti-salah']);

        $response = $this->from(route('view.signup'))
            ->post(route('user.signup'), $data);

        $response->assertRedirect(route('view.signup'));
        $response->assertSessionHasErrors('phone');
        $this->assertGuest('user');
    }
}
