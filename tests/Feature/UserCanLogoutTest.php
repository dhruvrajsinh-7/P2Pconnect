<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserCanLogoutTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_a_user_can_logout()
    {

        $user = User::factory()->create();

        $this->actingAs($user, 'api');


        $this->assertAuthenticated();

        $response = $this->post('/logout');

        $response->assertStatus(204);
        $this->assertGuest();
    }
}
