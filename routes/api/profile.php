<?php

use App\Http\Controllers\Api\Profile\AdvertiseController;
use App\Http\Controllers\Api\Profile\MatchController;
use App\Http\Controllers\Api\Profile\ClubController;
use App\Http\Controllers\Api\Profile\LeagueController;
use App\Http\Controllers\Api\Profile\PageController;
use App\Http\Controllers\Api\Profile\PostController;
use App\Http\Controllers\Api\Profile\StatusController;
use App\Http\Controllers\Api\Profile\StepController;
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
    Route::prefix('leagues')->group(function () {
        Route::get('/', [LeagueController::class, 'indexPaginate'])->name('profile.league.index');
        Route::get('/{league}', [LeagueController::class, 'show'])->name('profile.league.show');
        Route::post('/', [LeagueController::class, 'store'])->name('profile.league.store');
        Route::post('/{league}', [LeagueController::class, 'update'])->name('profile.league.update');
        Route::delete('/{league}', [LeagueController::class, 'destroy'])->name('profile.league.delete');
        Route::get('/clubs/{league}', [LeagueController::class, 'getClubs'])->name('profile.league.clubs');
        Route::post('/clubs/{league}', [LeagueController::class, 'storeClubs'])->name('profile.league.clubs.store');
    });

    // steps
    Route::prefix('steps')->group(function () {
        Route::get('/{step}', [StepController::class, 'getStepInfo'])->name('profile.step');
        Route::get('/create/{league}', [StepController::class, 'create'])->name('profile.step.create');
        Route::post('/{league}', [StepController::class, 'store'])->name('profile.step.store');
        Route::patch('/{step}', [StepController::class, 'update'])->name('profile.step.update');
        Route::delete('/{step}', [StepController::class, 'destroy'])->name('profile.step.destroy');
        Route::get('/clubs/{step}', [StepController::class, 'getClubs'])->name('profile.step.clubs');
        Route::post('/clubs/{step}', [StepController::class, 'storeClubs'])->name('profile.step.clubs.store');
    });

    // matches
    Route::prefix('matches')->group(function () {
        Route::get('/{matches}', [MatchController::class, 'show'])->name('profile.match.show');
        Route::patch('/{matches}', [MatchController::class, 'update'])->name('profile.match.update');
        Route::delete('/{matches}', [MatchController::class, 'destroy'])->name('profile.match.destroy');
        Route::post('/{step}', [MatchController::class, 'store'])->name('profile.match.store');
    });

     // advertises
     Route::prefix('advertise')->group(function () {
        Route::get('/', [AdvertiseController::class, 'indexPaginate'])->name('profile.advertise.index');
        Route::get('/{advertise}', [AdvertiseController::class, 'show'])->name('profile.advertise.show');
        Route::post('/', [AdvertiseController::class, 'store'])->name('profile.advertise.store');
        Route::post('/{advertise}', [AdvertiseController::class, 'update'])->name('profile.advertise.update');
        Route::delete('/{advertise}', [AdvertiseController::class, 'destroy'])->name('profile.advertise.delete');
    });

    // pages
    Route::prefix('pages')->group(function () {
        Route::get('/', [PageController::class, 'indexPaginate'])->name('profile.page.index');
        Route::get('/{page}', [PageController::class, 'show'])->name('profile.page.show');
        Route::post('/', [PageController::class, 'store'])->name('profile.page.store');
        Route::post('/{page}', [PageController::class, 'update'])->name('profile.page.update');
        Route::delete('/{page}', [PageController::class, 'destroy'])->name('profile.page.delete');
    });

});
