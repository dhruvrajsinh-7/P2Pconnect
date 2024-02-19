<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use  \App\Http\Resources\Post as PostResource;
use App\Http\Resources\PostCollection;
use App\Models\Friend;

class PostController extends Controller
{
    public function index()
    {
        $friends = Friend::friendships();
        if ($friends->isEmpty()) {
            return new PostCollection(request()->user()->posts);
        }
        return new PostCollection(
            Post::whereIn('user_id', [...$friends->pluck('user_id'), $friends->pluck('friend_id')])
                ->get()
        );
    }
    public function store()
    {
        $data = request()->validate([
            'body' => '',
            'image' => '',
            'width' => '',
            'height' => ''
        ]);
        if (isset($data['image'])) {
            $image = $data['image']->store('post-images', 'public');
        }
        $post = request()->user()->posts()->create([
            'body' => $data['body'], 'image' => !empty($image) ? 'storage/' . $image : null,
        ]);
        return new PostResource($post);
    }
}
