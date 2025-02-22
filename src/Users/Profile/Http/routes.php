<?php

use Illuminate\Support\Facades\Route;
use Src\Users\Profile\Http\Controllers\DeleteUserAvatarController;
use Src\Users\Profile\Http\Controllers\ProfilePageController;
use Src\Users\Profile\Http\Controllers\UpdateUserAvatarController;

Route::middleware(['web', 'auth'])->prefix('users/profile')->as('users.profile.')->group(function () {
    Route::get('/', ProfilePageController::class)->name('index');
    Route::get('/delete-avatar', DeleteUserAvatarController::class)->name('deleteAvatar');
    Route::post('/update', UpdateUserAvatarController::class)->name('updateAvatar');
});
