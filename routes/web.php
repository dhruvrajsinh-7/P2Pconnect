<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Auth::routes();
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('{any}', [App\Http\Controllers\AppController::class, 'index'])
    ->where('any', '.*')
    ->middleware('auth')
    ->name('home');
