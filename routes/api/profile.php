<?php

use App\Http\Controllers\Api\Profile\PostController;
use App\Http\Controllers\Api\Profile\StatusController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', 'auth'])->prefix('profile')->group(function() {

    // Posts
    Route::prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('profile.post.index');
        Route::post('/', [PostController::class, 'store'])->name('profile.post.store');
        Route::post('/{post}', [PostController::class, 'update'])->name('profile.post.update');
        Route::delete('/{post}', [PostController::class, 'destroy'])->name('profile.post.delete');
        Route::delete('/delete/{id}', [PostController::class, 'realDestroy'])->name('profile.post.real.delete');
    });
    // Status
    Route::prefix('status')->group(function () {
        Route::get('/', [StatusController::class, 'index'])->name('profile.status.index');
        Route::post('/', [StatusController::class, 'store'])->name('profile.status.store');
        Route::post('/{status}', [StatusController::class, 'update'])->name('profile.status.update');
        Route::delete('/{status}', [StatusController::class, 'destroy'])->name('profile.status.delete');
        Route::delete('/delete/{status}', [StatusController::class, 'realDestroy'])->name('profile.status.real.delete');
    });

});
