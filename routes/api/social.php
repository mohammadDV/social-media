<?php

use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\Social\BlockController;
use App\Http\Controllers\Api\Social\TicketController;
use App\Http\Controllers\Api\Social\CommentController;
use App\Http\Controllers\Api\Social\CountryController;
use App\Http\Controllers\Api\Social\FavoriteController;
use App\Http\Controllers\Api\Social\FollowController;
use App\Http\Controllers\Api\Social\LikeController;
use App\Http\Controllers\Api\Social\MemberController;
use App\Http\Controllers\Api\Social\NotificationController;
use App\Http\Controllers\Api\Social\SportController;
use App\Http\Controllers\Api\Social\StatusController;
use App\Http\Controllers\Api\Social\TicketSubjectController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', 'auth'])->group(function() {
    Route::post('/upload-image', [FileController::class, 'uploadImage'])->name('site.upload-image');
    Route::post('/upload-video', [FileController::class, 'uploadVideo'])->name('site.upload-video');
    Route::post('/upload-file', [FileController::class, 'uploadFile'])->name('site.upload-file');
    Route::get('/statuses/{user?}', [StatusController::class, 'index'])->name('site.statuses.index');
    Route::get('/status/all/{user}', [StatusController::class, 'getAllPerUser'])->name('site.statuses.all-per-user');
    Route::get('/post/all/{user}', [PostController::class, 'getAllPerUser'])->name('site.statuses.all-per-user');
    Route::get('/status/favorite/{user}', [StatusController::class, 'getFavorite'])->name('site.statuses.all-per-user');
    Route::post('/status/favorite/{status}', [StatusController::class, 'addFavorite'])->name('site.statuses.all-per-user');
    Route::get('/status/preview/{status}', [StatusController::class, 'getInfo'])->name('site.statuses.info');
    Route::get('/new-members', [MemberController::class, 'getNewMembers'])->name('site.new-members');
    Route::get('/congenial-members', [MemberController::class, 'getCongenialMembers'])->name('site.congenial-members');

    // Follow
    Route::get('/follow-info/{user?}', [FollowController::class, 'index'])->name('site.follow-info');
    Route::get('/followers/{user?}', [FollowController::class, 'getFollowers'])->name('site.followers');
    Route::get('/followings/{user?}', [FollowController::class, 'getFollowings'])->name('site.followings');
    Route::post('/follow-chaneg-status/{user}', [FollowController::class, 'changeFollowStatus'])->name('site.follow.change-status');
    Route::get('/is-follower/{user}', [FollowController::class, 'isFollower'])->name('site.is-follower');
    Route::post('/follow/{user}', [FollowController::class, 'store'])->name('site.follow.store');

    // Block
    Route::get('/block-users', [BlockController::class, 'index'])->name('site.block-users');
    Route::post('/block/{user}', [BlockController::class, 'store'])->name('site.block.store');

    // Comment
    Route::prefix('comment')->group(function () {
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
        Route::get('/clubs/{user?}', [FavoriteController::class, 'getClubs'])->name('social.favorite.clubs');
        Route::get('/clubs-limited/{user?}', [FavoriteController::class, 'getClubsLimited'])->name('social.favorite.clubs-limited');
        Route::post('/clubs/search', [FavoriteController::class, 'search'])->name('social.favorite.clubs.search');
        Route::post('/clubs/{club}', [FavoriteController::class, 'storeClub'])->name('social.favorite.clubs.store');
    });

    // user
    Route::prefix('user')->group(function () {
        Route::post('/search', [UserController::class, 'search'])->name('social.search.user');
        Route::get('/info/{user?}', [UserController::class, 'show'])->name('social.user.show');
    });

    // sport
    Route::prefix('sport')->group(function () {
        Route::get('/index', [SportController::class, 'index'])->name('social.sport.index');
    });

    // country
    Route::prefix('country')->group(function () {
        Route::get('/index', [CountryController::class, 'index'])->name('social.country.index');
    });

    // ticket subjects
    Route::prefix('ticket-subjects')->group(function () {
        Route::get('/index', [TicketSubjectController::class, 'index'])->name('social.ticket-subjects.index');
    });

    // notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'indexPaginate'])->name('profile.page.index');
    });


});
