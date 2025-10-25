<?php

namespace Tests\Feature\User;

use App\Models\User;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@test.com',
            'phone' => '081111',
            'address' => 'Jalan Tes',
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_guest_cannot_view_profile_edit_page()
    {
        $response = $this->get(route('user.view.profile'));

        $response->assertRedirect(route('view.login'));
    }

    public function test_authenticated_user_can_view_profile_edit_page()
    {
        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.view.profile'));

        $response->assertOk();
        $response->assertViewIs('users.profile.index');
        $response->assertViewHas('name', 'John Doe');
        $response->assertViewHas('email', 'john@test.com');
        $response->assertViewHas('phone', '081111');
        $response->assertViewHas('address', 'Jalan Tes');
    }

    public function test_guest_cannot_update_profile()
    {
        $response = $this->put(route('user.update'), [
            'name' => 'Nama Baru',
        ]);

        $response->assertRedirect(route('view.login'));
    }

    public function test_user_can_update_profile_data_without_changing_password()
    {
        $response = $this->actingAs($this->user, 'user')
            ->put(route('user.update'), [
                'name' => 'Nama Baru',
                'email' => 'user@test.com',
                'phone' => '08123456',
                'address' => 'Alamat Baru',
            ]);

        $response->assertRedirect(route('user.view.profile'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('Nama Baru', $this->user->name);
        $this->assertEquals('user@test.com', $this->user->email);
        $this->assertEquals('08123456', $this->user->phone);
        $this->assertEquals('Alamat Baru', $this->user->address);
        $this->assertTrue(Hash::check('password123', $this->user->password));
    }

    public function test_user_can_update_profile_and_password_with_correct_old_password()
    {
        $response = $this->actingAs($this->user, 'user')
            ->put(route('user.update'), [
                'name' => 'Nama Baru Lagi',
                'email' => 'user@test.com',
                'phone' => '08123456',
                'address' => 'Alamat Baru',
                'old_password' => 'password123',
                'password' => 'passwordbaru456',
            ]);

        $response->assertRedirect(route('user.view.profile'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('Nama Baru Lagi', $this->user->name);
        $this->assertTrue(Hash::check('passwordbaru456', $this->user->password));
    }

    public function test_profile_update_fails_if_old_password_is_incorrect()
    {
        $response = $this->actingAs($this->user, 'user')
            ->from(route('user.view.profile'))
            ->put(route('user.update'), [
                'name' => 'Nama Gagal',
                'email' => 'user@test.com',
                'phone' => '081234567',
                'address' => 'Alamat Gagal',
                'old_password' => 'password-salah',
                'password' => 'passwordbaru456',
            ]);

        $response->assertRedirect(route('user.view.profile'));
        $response->assertSessionHasErrors('old_password');

        $this->user->refresh();
        $this->assertNotEquals('Nama Gagal', $this->user->name);
        $this->assertTrue(Hash::check('password123', $this->user->password));
    }

    public function test_profile_update_fails_if_email_is_taken_by_another_user()
    {
        User::factory()->create(['email' => 'taken@test.com']);

        $response = $this->actingAs($this->user, 'user')
            ->from(route('user.view.profile'))
            ->put(route('user.update'), [
                'name' => 'Nama User',
                'email' => 'taken@test.com',
                'phone' => '08123456',
                'address' => 'Alamat',
            ]);

        $response->assertRedirect(route('user.view.profile'));
        $response->assertSessionHasErrors('email');
    }

    public function test_profile_update_fails_if_new_password_is_too_short()
    {
        $response = $this->actingAs($this->user, 'user')
            ->from(route('user.view.profile'))
            ->put(route('user.update'), [
                'name' => 'Nama User',
                'email' => 'user@test.com',
                'phone' => '08123456',
                'address' => 'Alamat',
                'old_password' => 'password123',
                'password' => '123',
            ]);

        $response->assertRedirect(route('user.view.profile'));
        $response->assertSessionHasErrors('password');
    }
}
