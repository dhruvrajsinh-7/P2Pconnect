<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserImagesTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }
    public function test_images_Can_be_uploaded()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = User::factory()->create(), 'api');
        $file = UploadedFile::fake()->image('user-image.jpg');
        $response = $this->post('/api/user-images', [
            'image' => $file,
            'width' => 850,
            'height' => 300,
            'location' => 'cover'
        ])->assertStatus(201);

        // Storage::disk('public')->assertExists('user-images/' . $file->hashName());
        $this->assertTrue(Storage::disk('public')->exists('user-images/' . $file->hashName()));
        $userImage = UserImage::first();
        $this->assertEquals('user-images/' . $file->hashName(), $userImage->path);
        $this->assertEquals('850', $userImage->width);
        $this->assertEquals('300', $userImage->height);
        $this->assertEquals('cover', $userImage->location);
        $this->assertEquals($user->id, $userImage->user_id);
        $response->assertJson([
            'data' => [
                'type' => 'user_images',
                'user_image_id' => $userImage->id,
                'attributes' => [
                    'path' => url($userImage->path),
                    'width' => $userImage->width,
                    'height' => $userImage->height,
                    'location' => $userImage->location
                ]
            ],
            'links' => [
                'self' => url('/users/' . $user->id)
            ]
        ]);
    }
    public function test_user_are_Returned_with_images()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = User::factory()->create(), 'api');
        $file = UploadedFile::fake()->image('user-image.jpg');
        $this->post('/api/user-images', [
            'image' => $file,
            'width' => 850,
            'height' => 300,
            'location' => 'cover'
        ])->assertStatus(201);
        $this->post('/api/user-images', [
            'image' => $file,
            'width' => 850,
            'height' => 300,
            'location' => 'profile'
        ])->assertStatus(201);
        $response = $this->get('/api/users/' . $user->id);
        $userImage = UserImage::first();
        $response->assertJson([
            'data' => [
                'type' => 'users',
                'user_id' => $user->id,
                'attributes' => [
                    'cover_images' => [
                        'data' => [
                            'type' => 'user_images',
                            'user_image_id' => 1,
                            'attributes' => []
                        ],
                    ],
                    'profile_image' => [
                        'data' => [
                            'type' => 'user_images',
                            'user_image_id' => 2,
                            'attributes' => []
                        ],
                    ]
                ]
            ],
        ]);
    }
}
