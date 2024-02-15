<?php

namespace Tests\Feature;

use App\Models\Friend;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FriendTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_a_user_can_send_a_friend_request()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = User::factory()->create(), 'api');
        $anotheruser = User::factory()->create();
        $response = $this->post('/api/friend-request', [
            'friend_id' => $anotheruser->id,
        ])->assertStatus(200);
        $friendRequest = Friend::first();
        $this->assertNotNull($friendRequest);
        $this->assertEquals($anotheruser->id, $friendRequest->friend_id);
        $this->assertEquals($user->id, $friendRequest->user_id);
        $response->assertJson([
            'data' => [
                'type' => 'friend-request',
                'friend_request_id' => $friendRequest->id,
                'attributes' => [
                    'confirmed_at' => null
                ]
            ], 'links' => [
                'self' => url('/users/' . $anotheruser->id)
            ]
        ]);
    }
    public function test_only_valid_user_Can_be_Friend()
    {
        $this->actingAs($user = User::factory()->create(), 'api');
        $response = $this->post('/api/friend-request', [
            'friend_id' => 123,
        ])->assertStatus(404);

        $this->assertNotNull(Friend::first());
        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'User Not Found',
                'detail' => 'Unable to locale the user with the given information'
            ]
        ]);
    }
    public function test_friend_request_can_be_accepted()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = User::factory()->create(), 'api');
        $anotheruser = User::factory()->create();
        $this->post('/api/friend-request', [
            'friend_id' => $anotheruser->id,
        ])->assertStatus(200);
        $response = $this->actingAs($anotheruser, 'api')->post('/api/friend-request-response', [
            'user_id' => $user->id,
            'status' => 1,
        ])->assertStatus(200);
        $friendRequest = Friend::first();
        $this->assertNotNull($friendRequest->confirmed_at);
        $this->assertInstanceOf(Carbon::class, $friendRequest->confirmed_at);
        $this->assertEquals(now()->startOfSecond(), $friendRequest->confirmed_at);
        $this->assertEquals(1, $friendRequest->status);
        $response->assertJson([
            'data' => [
                'type' => 'friend-request',
                'friend_request_id' => $friendRequest->id,
                'attributes' => [
                    'confirmed_at' => $friendRequest->confirmed_at->diffForHumans(),
                    'friend_id' => $friendRequest->friend_id,
                    'user_id' => $friendRequest->user_id,
                ]
            ], 'links' => [
                'self' => url('/users/' . $anotheruser->id),
            ]
        ]);
    }
    public function test_only_valid_friend_request_accepted()
    {
        $anotheruser = User::factory()->create();
        $response = $this->actingAs($anotheruser, 'api')->post('/api/friend-request-response', [
            'user_id' => 123,
            'status' => 1,
        ])->assertStatus(404);
        $this->assertNull(Friend::first());
        $response->assertJson([
            'error' => [
                'code' => 404,
                'title' => 'Friend Request Not Found',
                'detail' => 'unable to locate the friend request with the given information',
            ]
        ]);
    }
    public function test_only_the_recipient_can_accept_a_friend_request()
    {
        $this->actingAs($user = User::factory()->create(), 'api');
        $anotheruser = User::factory()->create();

        $this->post('/api/friend-request', [
            'friend_id' => $anotheruser->id,
        ])->assertStatus(200);
        $thirdUser = User::factory()->create();
        $response = $this->actingAs($anotheruser, 'api')->post('/api/friend-request-response', [
            'user_id' => $user->id,
            'status' => 1,
        ])->assertStatus(404);
        $friendRequest = Friend::first();
        $this->assertNull($friendRequest->confirmed_at);
        $this->assertNull($friendRequest->status);
        $response->assertJson([
            'error' => [
                'code' => 404,
                'title' => 'Friend Request Not Found',
                'detail' => 'unable to locate the friend request with the given information',
            ]
        ]);
    }
    public function test_a_friend_id_is_required_for_fr()
    {
        $response = $this->actingAs($user = User::factory()->create(), 'api')
            ->post('/api/friend-request', [
                'friend_id' => '',
            ])->assertStatus(422);
        $responseString = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('friend_id', $responseString['errors']['meta']);
    }
    public function test_a_user_id_and_status_required_for_fr()
    {
        $response = $this->actingAs($user = User::factory()->create(), 'api')
            ->post('/api/friend-request-response', [
                'user_id' => '',
                'status' => '',
            ])->assertStatus(422);
        $responseString = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('user_id', $responseString['errors']['meta']);
        $this->assertArrayHasKey('status', $responseString['errors']['meta']);
    }
    public function test_a_friendship_retrieved_when_fetching_profile()
    {
        $this->actingAs($user = User::factory()->create(), 'api');
        $anotheruser = User::factory()->create();
        $friendRequest = Friend::create([
            'user_id' => $user->id,
            'friend_id' => $anotheruser->id,
            'confirmed_at' => now()->subDay(),
            'status' => 1,
        ]);

        $this->get('/api/users/' . $anotheruser->id)->assertStatus(200)
            ->assertJson([
                'data' => [
                    'attributes' => [
                        'friendship' => [
                            'data' => [
                                'friend_request_id' => $friendRequest->id,
                                'attributes' => [
                                    'confirmed_at' => '1 day ago',
                                ]
                            ]
                        ]
                    ]
                ],
            ]);
    }
    public function test_an_inverse_friendship_retrieved_when_fetching_profile()
    {
        $this->actingAs($user = User::factory()->create(), 'api');
        $anotheruser = User::factory()->create();
        $friendRequest = Friend::create([
            'friend_id' => $user->id,
            'user_id' => $anotheruser->id,
            'confirmed_at' => now()->subDay(),
            'status' => 1,
        ]);

        $this->get('/api/users/' . $anotheruser->id)->assertStatus(200)
            ->assertJson([
                'data' => [
                    'attributes' => [
                        'friendship' => [
                            'data' => [
                                'friend_request_id' => $friendRequest->id,
                                'attributes' => [
                                    'confirmed_at' => '1 day ago',
                                ]
                            ]
                        ]
                    ]
                ],
            ]);
    }
    public function test_friend_request_can_be_ignored()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = User::factory()->create(), 'api');
        $anotheruser = User::factory()->create();
        $this->post('/api/friend-request', [
            'friend_id' => $anotheruser->id,
        ])->assertStatus(200);
        $response = $this->actingAs($anotheruser, 'api')->delete('/api/friend-request-response/delete', [
            'user_id' => $user->id,
        ])->assertStatus(204);
        $friendRequest = Friend::first();
        $this->assertNull($friendRequest);
        $response->assertNoContent();
    }
    public function test_only_the_recipient_can_ignore_a_friend_request()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = User::factory()->create(), 'api');
        $anotheruser = User::factory()->create();

        $this->post('/api/friend-request', [
            'friend_id' => $anotheruser->id,
        ])->assertStatus(200);
        $response = $this->actingAs($anotheruser, 'api')->delete('/api/friend-request-response/delete', [
            'user_id' => $user->id,
        ])->assertStatus(404);
        $friendRequest = Friend::first();
        $this->assertNull($friendRequest->confirmed_at);
        $this->assertNull($friendRequest->status);
        $response->assertJson([
            'error' => [
                'code' => 404,
                'title' => 'Friend Request Not Found',
                'detail' => 'unable to locate the friend request with the given information',
            ]
        ]);
    }
    public function test_a_user_id_and_status_required_for_ignoring_fr()
    {
        $response = $this->actingAs($user = User::factory()->create(), 'api')
            ->delete('/api/friend-request-response/delete', [
                'user_id' => '',
            ])->assertStatus(422);
        $responseString = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('user_id', $responseString['errors']['meta']);
    }
    public function test_a_user_can_send_a_friend_request_only_once()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = User::factory()->create(), 'api');
        $anotheruser = User::factory()->create();
        $this->post('/api/friend-request', [
            'friend_id' => $anotheruser->id,
        ])->assertStatus(200);
        $this->post('/api/friend-request', [
            'friend_id' => $anotheruser->id,
        ])->assertStatus(200);
        $friendRequest = Friend::all();
        $this->assertCount(1, $friendRequest);
    }
}
