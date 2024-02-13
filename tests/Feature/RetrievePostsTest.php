<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RetrievePostsTest extends TestCase
{
    use RefreshDatabase;
    use HasFactory;
    /**
     * A basic feature test example.
     */
    public function test_user_can_retrieve_posts(): void
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = User::factory()->create());
        $posts = Post::factory(2)->create(['user_id' => $user->id]);
        $response = $this->get('/api/posts');
        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    [
                        'data' => [
                            'type' => 'posts',
                            'post_id' => $posts->first()->id,
                            'attributes' => [
                                'body' => $posts->first()->body,
                            ]
                        ],

                    ],
                    [
                        'data' => [
                            'type' => 'posts',
                            'post_id' => $posts->last()->id,
                            'attributes' => [
                                'body' => $posts->last()->body,
                            ]
                        ]
                    ]
                ],
                'links' => [
                    'self' => url('/posts/'),
                ]
            ]
        );
    }
    public function test_a_user_can_only_retrieve_their_posts(): void
    {
        $this->actingAs($user = User::factory()->create(), 'api');
        $posts = Post::factory(2)->create(['user_id' => $user->id]);
        $response = $this->get('/api/posts');
        $response->assertStatus(200);
        $response->assertExactJson(
            [
                'data' => [],
                'links' => [
                    'self' => url('/posts'),
                ]
            ]
        );
    }
}
