<?php

use App\Http\Controllers\Api\ValidationController;
use App\Http\Controllers\SitemapController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/sitemap.xml', [SitemapController::class, 'generateSitemap']);

Route::post('/validation/{requestName}', [ValidationController::class, 'index']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware(['auth:sanctum'])->get('/profile', function() {
    return response()->json([
        "title" => "yes"
    ]);
});



require __DIR__.'/api/auth.php';
require __DIR__.'/api/site.php';
require __DIR__.'/api/social.php';
require __DIR__.'/api/profile.php';
