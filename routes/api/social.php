<?php

use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\Social\CommentController;
use App\Http\Controllers\Api\Social\FavoriteController;
use App\Http\Controllers\Api\Social\FollowController;
use App\Http\Controllers\Api\Social\LikeController;
use App\Http\Controllers\Api\Social\MemberController;
use App\Http\Controllers\Api\social\StatusController;
use App\Models\User;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', 'auth'])->group(function() {
    Route::post('/upload-image', [FileController::class, 'uploadImage'])->name('site.upload-image');
    Route::post('/upload-video', [FileController::class, 'uploadVideo'])->name('site.upload-video');
    Route::get('/statuses/{user?}', [StatusController::class, 'index'])->name('site.statuses.index');
    Route::get('/follow-info/{user?}', [FollowController::class, 'index'])->name('site.follow-info');
    Route::get('/followers/{user?}', [FollowController::class, 'getFollowers'])->name('site.followers');
    Route::get('/followings/{user?}', [FollowController::class, 'getFollowings'])->name('site.followings');
    Route::post('/follow/{user}', [FollowController::class, 'store'])->name('site.follow.store');
    Route::get('/new-members', [MemberController::class, 'getNewMembers'])->name('site.new-members');
    Route::get('/congenial-members', [MemberController::class, 'getCongenialMembers'])->name('site.congenial-members');

    // Comment
    Route::prefix('comment')->group(function () {
        Route::get('/post/{post}', [CommentController::class, 'getPostComments'])->name('social.comment.post');
        Route::post('/post/{post}', [CommentController::class, 'storePostComment'])->name('social.comment.post.store');
        Route::get('/status/{status}', [CommentController::class, 'getStatusComments'])->name('social.comment.status');
        Route::post('/status/{status}', [CommentController::class, 'storeStatusComment'])->name('social.comment.status.store');
    });

    // Like
    Route::prefix('like')->group(function () {
        Route::post('/all', [LikeController::class, 'getLikes'])->name('social.like.get.all');
        Route::post('/count', [LikeController::class, 'getLikeCount'])->name('social.like.get.count');
        Route::post('/', [LikeController::class, 'store'])->name('social.like.store');
    });

    // Favorite
    Route::prefix('favorite')->group(function () {
        Route::get('/clubs', [FavoriteController::class, 'getClubs'])->name('social.favorite.clubs');
        Route::post('/clubs/search', [FavoriteController::class, 'search'])->name('social.favorite.clubs.search');
        Route::post('/clubs/{club}', [FavoriteController::class, 'storeClub'])->name('social.favorite.clubs.store');
    });
});
