<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['cors'])->group(function () {

    Route::get('/article/article_detail/{id}', function ($id) {

        return response()->json([
            "article" => [
            "id" => $id,
            "title" => $id . "داستان روسیه از اول",
            "file" => "https://bpluspodcast.com/wp-content/uploads/2023/07/%D8%AA%D8%A7%D8%B1%DB%8C%D8%AE-%D8%B1%D9%88%D8%B3%DB%8C%D9%87-%D8%B9%D9%84%DB%8C-%D8%A8%D9%86%D8%AF%D8%B1%DB%8C-%DA%A9%D8%A7%D9%86%D8%A7%D9%84-%DB%8C%D9%88%D8%AA%DB%8C%D9%88%D8%A8-%D8%A8%DB%8C-%D9%BE%D9%84%D8%A7%D8%B3-1024x576.jpg",
            "content" => "نویسنده: بهجت بندری، علی بندری روسیه رو که نگاه می‌کنیم امروز چی می‌بینیم؟ یک کشور خیلی خیلی بزرگ، قدرتمند، که از جایگاه خودش در دنیا راضی نیست. زمان تزارها همین…",
            "create_date" => '2022',
            "update_date" => '2022',
            ]
            ]
        , Response::HTTP_OK);
    });
    Route::get('/article/remove_article/{id}', function () {

        return response()->json([
            "message" => "ok"
            ]
        , Response::HTTP_OK);
    });
    Route::post('/article/edit_article/{id}', function () {

        return response()->json([
            "message" => "ok"
            ]
        , Response::HTTP_OK);
    });
    Route::post('/article/create_article/', function () {

        return response()->json([
            "message" => "ok"
            ]
        , Response::HTTP_CREATED);
    });
    Route::get('/article/article_list', function () {

        $data = [];
        for ($i = 1; $i<10 ; $i++) {
            $data[] = [
                "id" => $i,
                "title" => $i . "داستان روسیه از اول",
                "file" => "https://bpluspodcast.com/wp-content/uploads/2023/07/%D8%AA%D8%A7%D8%B1%DB%8C%D8%AE-%D8%B1%D9%88%D8%B3%DB%8C%D9%87-%D8%B9%D9%84%DB%8C-%D8%A8%D9%86%D8%AF%D8%B1%DB%8C-%DA%A9%D8%A7%D9%86%D8%A7%D9%84-%DB%8C%D9%88%D8%AA%DB%8C%D9%88%D8%A8-%D8%A8%DB%8C-%D9%BE%D9%84%D8%A7%D8%B3-1024x576.jpg",
                "content" => "نویسنده: بهجت بندری، علی بندری روسیه رو که نگاه می‌کنیم امروز چی می‌بینیم؟ یک کشور خیلی خیلی بزرگ، قدرتمند، که از جایگاه خودش در دنیا راضی نیست. زمان تزارها همین…",
                "create_date" => '2022',
                "update_date" => '2022',
            ];
        }

        return response()->json([
            "data" => $data,
            "count" => 2
            ]
        );
    });
});

