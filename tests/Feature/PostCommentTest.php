<?php

namespace Tests\Feature;

use App\Models\Comment as ModelsComment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostCommentTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_a_user_can_post_a_comment()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = User::factory()->create(), 'api');
        $post = Post::factory()->create(['id' => 123]);
        $response = $this->post('/api/posts/' . $post->id . '/comment', [
            'body' => 'A great comment here.'
        ])->assertStatus(200);
        $comment = ModelsComment::first();
        $this->assertCount(1, ModelsComment::all());
        $this->assertEquals($user->id, $comment->user_id);
        $this->assertEquals($post->id, $comment->post_id);
        $this->assertEquals('A great comment here.', $comment->body);
        $response->assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'comments',
                        'comment_id' => 1,
                        'attributes' => [
                            'commented_by' => [
                                'user_id' => $user->id,
                                'data' => [
                                    'attributes' => [
                                        'name' => $user->name,
                                    ]
                                ]
                            ],
                            'body' => 'A great comment here.',
                            'commented_at' => $comment->created_at->diffForHumans()
                        ]
                    ],
                    'links' => [
                        'self' => url('/posts/123')
                    ]
                ]
            ],
            'links' => [
                'self' => url('/posts')
            ]
        ]);
    }
    public function test_a_body_is_required_to_leave_a_comment_on_a_post()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = User::factory()->create(), 'api');
        $post = Post::factory()->create(['id' => 123]);
        $response = $this->post('/api/posts/' . $post->id . '/comment', [
            'body' => ''
        ])->assertStatus(422);
        $responseString = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('body', $responseString['errors']['meta']);
    }

    public function test_post_return_with_comments()
    {
        $this->actingAs($user = User::factory()->create(), 'api');
        $post = Post::factory()->create(['id' => 123, 'user_id' => $user->id]);
        $this->post('/api/posts/' . $post->id . '/comment', [
            'body' => 'A great comment here'
        ]);
        $response = $this->get('/api/posts');
        $comment = ModelsComment::first();
        $response->assertStatus(200)->assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'posts',
                        'attributes' => [
                            'comments' => [
                                'data' => [
                                    [
                                        'data' => [
                                            'type' => 'comments',
                                            'comment_id' => 1,
                                            'attributes' => [
                                                'commented_by' => [
                                                    'user_id' => $user->id,
                                                    'data' => [
                                                        'attributes' => [
                                                            'name' => $user->name,
                                                        ]
                                                    ]
                                                ],
                                                'body' => 'A great comment here.',
                                                'commented_at' => $comment->created_at->diffForHumans()
                                            ]
                                        ],
                                        'links' => [
                                            'self' => url('/posts/123')
                                        ]
                                    ]
                                ],
                                'comment_count' => 1,
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }
}
