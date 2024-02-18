<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Http\Resources\User as UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Comment extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => [
                'type' => 'comments',
                'comment_id' => 1,
                'attributes' => [
                    'commented_by' => new UserResource($this->user),
                    'body' => $this->body,
                    'commented_at' => $this->created_at->diffForHumans()
                ]
            ],
            'links' => [
                'self' => url('/posts/' . $this->post_id)
            ]
        ];
    }
}
