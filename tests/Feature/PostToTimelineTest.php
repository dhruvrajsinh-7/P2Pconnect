<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostToTimelineTest extends TestCase
{
    use RefreshDatabase;
    use HasFactory;
    /**
     * A basic feature test example.
     */
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }
    public function test_a_user_can_post_a_text_post(): void
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = User::factory()->create(), 'api');
        $response = $this->post('/api/posts', [
            'body' => 'Testing body'
        ]);
        $post = Post::first();
        $this->assertCount(1, Post::all());
        $this->assertEquals($user->id, $post->user_id);
        $this->assertEquals('Testing body', $post->body);
        $response->assertStatus(201)->assertJson(
            [
                'data' => [
                    'type' => 'posts',
                    'post_id' => $post->id,
                    'attributes' => [
                        'posted_by' => [
                            'data' => [
                                'attributes' => [
                                    'name' => $user->name
                                ]
                            ]
                        ],
                        'body' => 'Testing body'
                    ]
                ],
                'links' => [
                    'self' => url('/posts/' . $post->id),
                ]
            ]
        );
    }
    public function test_a_user_can_post_a_text_post_with_image(): void
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = User::factory()->create(), 'api');
        $file = UploadedFile::fake()->image('userPost.jpg');
        $response = $this->post('/api/posts', [
            'body' => 'Testing body',
            'image' => $file,
            'width' => 100,
            'height' => 100
        ]);
        $this->assertTrue(Storage::disk('public')->exists('post-images/' . $file->hashName()));
        $post = Post::first();
        $this->assertCount(1, Post::all());
        $this->assertEquals($user->id, $post->user_id);
        $this->assertEquals('Testing body', $post->body);
        $response->assertStatus(201)->assertJson(
            [
                'data' => [
                    'attributes' => [
                        'body' => 'Testing body',
                        'image' => url('storage/' . $file),
                    ]
                ],
            ],
        );
    }
}
