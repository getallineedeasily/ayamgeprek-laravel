<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'user');

        $this->assertAuthenticated('user');

        $response = $this->post(route('user.logout'));

        $response->assertRedirect('/');
        $this->assertGuest('user');
    }
}
