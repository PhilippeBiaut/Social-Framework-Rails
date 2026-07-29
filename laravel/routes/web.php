<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::livewire('/', 'feed')->name('feed');
    Route::livewire('/posts/{post}', 'post-page')->name('posts.show');

    Route::livewire('/people', 'people')->name('users.index');
    Route::livewire('/settings/profile', 'profile-edit')->name('profile.edit');

    Route::livewire('/users/{user}', 'profile')->name('users.show');
    Route::livewire('/users/{user}/following', 'connections')->name('users.following');
    Route::livewire('/users/{user}/followers', 'connections')->name('users.followers');
});
