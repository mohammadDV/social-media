<?php

use App\Http\Controllers\Api\Social\FollowController;
use App\Http\Controllers\Api\Social\MemberController;
use App\Http\Controllers\Api\social\StatusController;
use App\Models\User;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', 'auth'])->group(function() {
    Route::get('/statuses/{user?}', [StatusController::class, 'index'])->name('site.statuses.index');
    Route::get('/follow-info/{user?}', [FollowController::class, 'index'])->name('site.follow-info');
    Route::get('/followers/{user?}', [FollowController::class, 'getFollowers'])->name('site.followers');
    Route::get('/followings/{user?}', [FollowController::class, 'getFollowings'])->name('site.followings');
    Route::post('/follow/{user}', [FollowController::class, 'store'])->name('site.follow.store');
    Route::get('/new-members', [MemberController::class, 'getNewMembers'])->name('site.new-members');
    Route::get('/congenial-members', [MemberController::class, 'getCongenialMembers'])->name('site.congenial-members');
});
