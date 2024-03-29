<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\FriendRequestController;
use App\Http\Controllers\FriendRequestResponseController;
use App\Http\Controllers\Logoutcontroller;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserImageController;
use App\Http\Controllers\UserPostController;
use App\Http\Resources\User;
use Illuminate\Support\Facades\Route;


Route::view('/login', 'auth.login')->name('login');

Route::middleware(['auth:api'])->group(function () {
    Route::get('auth-user', [AuthUserController::class, 'show']);
    Route::apiResources([
        '/posts' => PostController::class,
        '/posts/{post}/like' => PostLikeController::class,
        '/posts/{post}/comment' => PostCommentController::class,
        '/users' => UserController::class,
        '/users/{user}/posts' => UserPostController::class,
        '/friend-request' => FriendRequestController::class,
        '/friend-request-response' => FriendRequestResponseController::class,
        '/user-images' => UserImageController::class
    ]);
    // Route::apiResource('users', UserController::class);

    // Route::apiResource('posts', PostController::class);
    // Route::apiResource('users.posts', UserPostController::class);
    // Route::apiResource('friend-request', FriendRequestController::class);
    // Route::apiResource('friend-request-response', FriendRequestResponseController::class);
    // Route::apiResource('/posts/{post}/like', PostLikeController::class);
    // Route::apiResource('/posts/{post}/comment', PostCommentController::class);
    // Route::apiResource('user-images', UserImageController::class);
});
Route::apiResource('/logout', Logoutcontroller::class);
