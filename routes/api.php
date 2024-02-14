<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPostController;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth:api'])->group(function () {
    Route::apiResources([
        'posts' => PostController::class,
        'users' => UserController::class,
        'users/{user}/posts' => UserPostController::class
    ]);
});
