<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_logout()
    {
        $user = Admin::factory()->create();

        $this->actingAs($user, 'admin');

        $this->assertAuthenticated('admin');

        $response = $this->post(route('admin.logout'));

        $response->assertRedirect('/');
        $this->assertGuest('admin');
    }
}
