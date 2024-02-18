<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;
    use HasFactory;
    /**
     * A basic feature test example.
     */
    public function test_a_user_can_like_post()
    {
        $this->actingAs($user = User::factory()->create(), 'api');
        $post = Post::factory()->create(['id' => 123]);
        $response = $this->post('/api/posts/' . $post->id . '/like')->assertStatus(200);
        $this->assertCount(1, $user->likedPosts);
        $response->assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'likes',
                        'like_id' => 1,
                        'attributes' => [],
                    ],
                    'links' => [
                        'self' => url('/posts/123'),
                    ],
                ],
            ],
            'links' => [
                'self' => url('/posts'),
            ],
        ]);
    }
    public function test_posts_are_return_with_likes()
    {
        $this->actingAs($user = User::factory()->create(), 'api');
        $post = Post::factory()->create(['id' => 123, 'user_id' => $user->id]);
        $this->post('/api/posts/' . $post->id . '/like')->assertStatus(200);
        $response = $this->get('/api/posts')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'data' => [
                            'type' => 'posts',
                            'attributes' => [
                                'likes' => [
                                    'data' => [
                                        [
                                            'data' => [
                                                'type' => 'likes',
                                                'like_id' => 1,
                                                'attributes' => []
                                            ]
                                        ]
                                    ],
                                    'like_count' => 1,
                                    'user_likes_post' => true,
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }
}
