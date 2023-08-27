<?php

use App\Http\Controllers\Api\LeagueController;
use App\Http\Controllers\Api\LiveController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;


Route::get('/posts', [PostController::class, 'index'])->name('site.posts.index');
Route::get('/leagues', [LeagueController::class, 'index'])->name('site.leagues.index');
Route::get('/leagues/{league}', [LeagueController::class, 'getLeagueInfo'])->name('site.league.info');
Route::get('/lives', [LiveController::class, 'index'])->name('site.lives.index');

// Route::middleware(['auth:sanctum'])->post('/logout', [AuthController::class, 'logout'])
//                 ->middleware('auth')
//                 ->name('logout');
