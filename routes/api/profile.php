<?php

use App\Http\Controllers\Api\Profile\ClubController;
use App\Http\Controllers\Api\Profile\PostController;
use App\Http\Controllers\Api\Profile\StatusController;
use App\Http\Controllers\Api\Profile\UserController;
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

    // userInfo
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'indexPaginate'])->name('profile.users.pagination');
        Route::get('/info', [UserController::class, 'show'])->name('profile.user.show');
        Route::post('/', [UserController::class, 'store'])->name('profile.user.store');
        Route::post('/{user}', [UserController::class, 'update'])->name('profile.user.update');
        Route::patch('/password', [UserController::class, 'updatePassword'])->name('profile.user.password.update');
    });

    // clubs
    Route::prefix('clubs')->group(function () {
        Route::get('/', [ClubController::class, 'indexPaginate'])->name('profile.club.index');
        Route::get('/{club}', [ClubController::class, 'show'])->name('profile.club.show');
        Route::post('/', [ClubController::class, 'store'])->name('profile.club.store');
        Route::post('/{club}', [ClubController::class, 'update'])->name('profile.club.update');
        Route::delete('/{club}', [ClubController::class, 'destroy'])->name('profile.club.delete');
    });

    // leagues
    Route::prefix('league')->group(function () {
        Route::get('/', [LeagueController::class, 'index'])->name('profile.league.index');
        Route::post('/get', [LeagueController::class, 'get'])->name('profile.league.get');
        Route::get('/create', [LeagueController::class, 'create'])->name('profile.league.create');
        Route::post('/', [LeagueController::class, 'store'])->name('profile.league.store');
        Route::patch('/{league}', [LeagueController::class, 'update'])->name('profile.league.update');
        Route::get('/edit/{league}', [LeagueController::class, 'edit'])->name('profile.league.edit');
        Route::get('/delete/{league}', [LeagueController::class, 'destroy'])->name('profile.league.delete');
        Route::get('/clubs/{league}', [LeagueController::class, 'getClubs'])->name('profile.league.clubs');
        Route::post('/clubs/{league}', [LeagueController::class, 'storeClubs'])->name('profile.league.clubs.store');
    });

});
