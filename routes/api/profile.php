<?php

use App\Http\Controllers\Api\Profile\PostController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', 'auth'])->prefix('profile')->group(function() {
    Route::prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('profile.post.index');
        Route::post('/', [PostController::class, 'store'])->name('profile.post.store');
        Route::post('/{post}', [PostController::class, 'update'])->name('profile.post.update');
        Route::delete('/{post}', [PostController::class, 'destroy'])->name('profile.post.delete');
        Route::delete('/delete/{id}', [PostController::class, 'realDestroy'])->name('profile.post.real.delete');
    });
});
