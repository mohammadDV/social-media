<?php

use App\Http\Controllers\Api\ValidationController;
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

Route::get('/validation/{requestName}', [ValidationController::class, 'index']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware(['auth:sanctum'])->get('/profile', function() {
    return response()->json([
        "title" => "yes"
    ]);
});

Route::middleware(['cors'])->group(function () {

    Route::get('/article/article_list', function () {
        return response()->json([
            "data" => [
                [
                    "id" => 1,
                    "title" => "1داستان روسیه از اول",
                    "file" => "https://bpluspodcast.com/wp-content/uploads/2023/07/%D8%AA%D8%A7%D8%B1%DB%8C%D8%AE-%D8%B1%D9%88%D8%B3%DB%8C%D9%87-%D8%B9%D9%84%DB%8C-%D8%A8%D9%86%D8%AF%D8%B1%DB%8C-%DA%A9%D8%A7%D9%86%D8%A7%D9%84-%DB%8C%D9%88%D8%AA%DB%8C%D9%88%D8%A8-%D8%A8%DB%8C-%D9%BE%D9%84%D8%A7%D8%B3-1024x576.jpg",
                    "content" => "نویسنده: بهجت بندری، علی بندری روسیه رو که نگاه می‌کنیم امروز چی می‌بینیم؟ یک کشور خیلی خیلی بزرگ، قدرتمند، که از جایگاه خودش در دنیا راضی نیست. زمان تزارها همین…",
                    "create_date" => 1,
                    "update_date" => 1,
                ],
                [
                    "id" => 2,
                    "title" => "2داستان روسیه از اول",
                    "file" => "https://bpluspodcast.com/wp-content/uploads/2023/07/%D8%AA%D8%A7%D8%B1%DB%8C%D8%AE-%D8%B1%D9%88%D8%B3%DB%8C%D9%87-%D8%B9%D9%84%DB%8C-%D8%A8%D9%86%D8%AF%D8%B1%DB%8C-%DA%A9%D8%A7%D9%86%D8%A7%D9%84-%DB%8C%D9%88%D8%AA%DB%8C%D9%88%D8%A8-%D8%A8%DB%8C-%D9%BE%D9%84%D8%A7%D8%B3-1024x576.jpg",
                    "content" => "نویسنده: بهجت بندری، علی بندری روسیه رو که نگاه می‌کنیم امروز چی می‌بینیم؟ یک کشور خیلی خیلی بزرگ، قدرتمند، که از جایگاه خودش در دنیا راضی نیست. زمان تزارها همین…",
                    "create_date" => 1,
                    "update_date" => 1,
                ],
                [
                    "id" => 3,
                    "title" => "3داستان روسیه از اول",
                    "file" => "https://bpluspodcast.com/wp-content/uploads/2023/07/%D8%AA%D8%A7%D8%B1%DB%8C%D8%AE-%D8%B1%D9%88%D8%B3%DB%8C%D9%87-%D8%B9%D9%84%DB%8C-%D8%A8%D9%86%D8%AF%D8%B1%DB%8C-%DA%A9%D8%A7%D9%86%D8%A7%D9%84-%DB%8C%D9%88%D8%AA%DB%8C%D9%88%D8%A8-%D8%A8%DB%8C-%D9%BE%D9%84%D8%A7%D8%B3-1024x576.jpg",
                    "content" => "نویسنده: بهجت بندری، علی بندری روسیه رو که نگاه می‌کنیم امروز چی می‌بینیم؟ یک کشور خیلی خیلی بزرگ، قدرتمند، که از جایگاه خودش در دنیا راضی نیست. زمان تزارها همین…",
                    "create_date" => 1,
                    "update_date" => 1,
                ],
                [
                    "id" => 4,
                    "title" => "5داستان روسیه از اول",
                    "file" => "https://bpluspodcast.com/wp-content/uploads/2023/07/%D8%AA%D8%A7%D8%B1%DB%8C%D8%AE-%D8%B1%D9%88%D8%B3%DB%8C%D9%87-%D8%B9%D9%84%DB%8C-%D8%A8%D9%86%D8%AF%D8%B1%DB%8C-%DA%A9%D8%A7%D9%86%D8%A7%D9%84-%DB%8C%D9%88%D8%AA%DB%8C%D9%88%D8%A8-%D8%A8%DB%8C-%D9%BE%D9%84%D8%A7%D8%B3-1024x576.jpg",
                    "content" => "نویسنده: بهجت بندری، علی بندری روسیه رو که نگاه می‌کنیم امروز چی می‌بینیم؟ یک کشور خیلی خیلی بزرگ، قدرتمند، که از جایگاه خودش در دنیا راضی نیست. زمان تزارها همین…",
                    "create_date" => 1,
                    "update_date" => 1,
                ],
                [
                    "id" => 5,
                    "title" => "6داستان روسیه از اول",
                    "file" => "https://bpluspodcast.com/wp-content/uploads/2023/07/%D8%AA%D8%A7%D8%B1%DB%8C%D8%AE-%D8%B1%D9%88%D8%B3%DB%8C%D9%87-%D8%B9%D9%84%DB%8C-%D8%A8%D9%86%D8%AF%D8%B1%DB%8C-%DA%A9%D8%A7%D9%86%D8%A7%D9%84-%DB%8C%D9%88%D8%AA%DB%8C%D9%88%D8%A8-%D8%A8%DB%8C-%D9%BE%D9%84%D8%A7%D8%B3-1024x576.jpg",
                    "content" => "نویسنده: بهجت بندری، علی بندری روسیه رو که نگاه می‌کنیم امروز چی می‌بینیم؟ یک کشور خیلی خیلی بزرگ، قدرتمند، که از جایگاه خودش در دنیا راضی نیست. زمان تزارها همین…",
                    "create_date" => 1,
                    "update_date" => 1,
                ],
                [
                    "id" => 6,
                    "title" => "داستان روسیه از ا6ول",
                    "file" => "https://bpluspodcast.com/wp-content/uploads/2023/07/%D8%AA%D8%A7%D8%B1%DB%8C%D8%AE-%D8%B1%D9%88%D8%B3%DB%8C%D9%87-%D8%B9%D9%84%DB%8C-%D8%A8%D9%86%D8%AF%D8%B1%DB%8C-%DA%A9%D8%A7%D9%86%D8%A7%D9%84-%DB%8C%D9%88%D8%AA%DB%8C%D9%88%D8%A8-%D8%A8%DB%8C-%D9%BE%D9%84%D8%A7%D8%B3-1024x576.jpg",
                    "content" => "نویسنده: بهجت بندری، علی بندری روسیه رو که نگاه می‌کنیم امروز چی می‌بینیم؟ یک کشور خیلی خیلی بزرگ، قدرتمند، که از جایگاه خودش در دنیا راضی نیست. زمان تزارها همین…",
                    "create_date" => 1,
                    "update_date" => 1,
                ],
                [
                    "id" => 7,
                    "title" => "7داستان روسیه از اول",
                    "file" => "https://bpluspodcast.com/wp-content/uploads/2023/07/%D8%AA%D8%A7%D8%B1%DB%8C%D8%AE-%D8%B1%D9%88%D8%B3%DB%8C%D9%87-%D8%B9%D9%84%DB%8C-%D8%A8%D9%86%D8%AF%D8%B1%DB%8C-%DA%A9%D8%A7%D9%86%D8%A7%D9%84-%DB%8C%D9%88%D8%AA%DB%8C%D9%88%D8%A8-%D8%A8%DB%8C-%D9%BE%D9%84%D8%A7%D8%B3-1024x576.jpg",
                    "content" => "نویسنده: بهجت بندری، علی بندری روسیه رو که نگاه می‌کنیم امروز چی می‌بینیم؟ یک کشور خیلی خیلی بزرگ، قدرتمند، که از جایگاه خودش در دنیا راضی نیست. زمان تزارها همین…",
                    "create_date" => 1,
                    "update_date" => 1,
                ],
                [
                    "id" => 8,
                    "title" => "8داستان روسیه از اول",
                    "file" => "https://bpluspodcast.com/wp-content/uploads/2023/07/%D8%AA%D8%A7%D8%B1%DB%8C%D8%AE-%D8%B1%D9%88%D8%B3%DB%8C%D9%87-%D8%B9%D9%84%DB%8C-%D8%A8%D9%86%D8%AF%D8%B1%DB%8C-%DA%A9%D8%A7%D9%86%D8%A7%D9%84-%DB%8C%D9%88%D8%AA%DB%8C%D9%88%D8%A8-%D8%A8%DB%8C-%D9%BE%D9%84%D8%A7%D8%B3-1024x576.jpg",
                    "content" => "نویسنده: بهجت بندری، علی بندری روسیه رو که نگاه می‌کنیم امروز چی می‌بینیم؟ یک کشور خیلی خیلی بزرگ، قدرتمند، که از جایگاه خودش در دنیا راضی نیست. زمان تزارها همین…",
                    "create_date" => 1,
                    "update_date" => 1,
                ],
                [
                    "id" => 9,
                    "title" => "9داستان روسیه از اول",
                    "file" => "https://bpluspodcast.com/wp-content/uploads/2023/07/%D8%AA%D8%A7%D8%B1%DB%8C%D8%AE-%D8%B1%D9%88%D8%B3%DB%8C%D9%87-%D8%B9%D9%84%DB%8C-%D8%A8%D9%86%D8%AF%D8%B1%DB%8C-%DA%A9%D8%A7%D9%86%D8%A7%D9%84-%DB%8C%D9%88%D8%AA%DB%8C%D9%88%D8%A8-%D8%A8%DB%8C-%D9%BE%D9%84%D8%A7%D8%B3-1024x576.jpg",
                    "content" => "نویسنده: بهجت بندری، علی بندری روسیه رو که نگاه می‌کنیم امروز چی می‌بینیم؟ یک کشور خیلی خیلی بزرگ، قدرتمند، که از جایگاه خودش در دنیا راضی نیست. زمان تزارها همین…",
                    "create_date" => 1,
                    "update_date" => 1,
                ],
                [
                    "id" => 10,
                    "title" => "10داستان روسیه از اول",
                    "file" => "https://bpluspodcast.com/wp-content/uploads/2023/07/%D8%AA%D8%A7%D8%B1%DB%8C%D8%AE-%D8%B1%D9%88%D8%B3%DB%8C%D9%87-%D8%B9%D9%84%DB%8C-%D8%A8%D9%86%D8%AF%D8%B1%DB%8C-%DA%A9%D8%A7%D9%86%D8%A7%D9%84-%DB%8C%D9%88%D8%AA%DB%8C%D9%88%D8%A8-%D8%A8%DB%8C-%D9%BE%D9%84%D8%A7%D8%B3-1024x576.jpg",
                    "content" => "نویسنده: بهجت بندری، علی بندری روسیه رو که نگاه می‌کنیم امروز چی می‌بینیم؟ یک کشور خیلی خیلی بزرگ، قدرتمند، که از جایگاه خودش در دنیا راضی نیست. زمان تزارها همین…",
                    "create_date" => 1,
                    "update_date" => 1,
                ],
                [
                    "id" => 11,
                    "title" => "11داستان روسیه از اول",
                    "file" => "https://bpluspodcast.com/wp-content/uploads/2023/07/%D8%AA%D8%A7%D8%B1%DB%8C%D8%AE-%D8%B1%D9%88%D8%B3%DB%8C%D9%87-%D8%B9%D9%84%DB%8C-%D8%A8%D9%86%D8%AF%D8%B1%DB%8C-%DA%A9%D8%A7%D9%86%D8%A7%D9%84-%DB%8C%D9%88%D8%AA%DB%8C%D9%88%D8%A8-%D8%A8%DB%8C-%D9%BE%D9%84%D8%A7%D8%B3-1024x576.jpg",
                    "content" => "نویسنده: بهجت بندری، علی بندری روسیه رو که نگاه می‌کنیم امروز چی می‌بینیم؟ یک کشور خیلی خیلی بزرگ، قدرتمند، که از جایگاه خودش در دنیا راضی نیست. زمان تزارها همین…",
                    "create_date" => 1,
                    "update_date" => 1,
                ],
                [
                    "id" => 12,
                    "title" => "12داستان روسیه از اول",
                    "file" => "https://bpluspodcast.com/wp-content/uploads/2023/07/%D8%AA%D8%A7%D8%B1%DB%8C%D8%AE-%D8%B1%D9%88%D8%B3%DB%8C%D9%87-%D8%B9%D9%84%DB%8C-%D8%A8%D9%86%D8%AF%D8%B1%DB%8C-%DA%A9%D8%A7%D9%86%D8%A7%D9%84-%DB%8C%D9%88%D8%AA%DB%8C%D9%88%D8%A8-%D8%A8%DB%8C-%D9%BE%D9%84%D8%A7%D8%B3-1024x576.jpg",
                    "content" => "نویسنده: بهجت بندری، علی بندری روسیه رو که نگاه می‌کنیم امروز چی می‌بینیم؟ یک کشور خیلی خیلی بزرگ، قدرتمند، که از جایگاه خودش در دنیا راضی نیست. زمان تزارها همین…",
                    "create_date" => 1,
                    "update_date" => 1,
                ],
            ],
            "count" => 11
            ]
        );
    });
});



require __DIR__.'/api/auth.php';
require __DIR__.'/api/site.php';
require __DIR__.'/api/social.php';
require __DIR__.'/api/profile.php';
