<?php

namespace App\Services\Image;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Image;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageCacheService
{
    public function cache($imagePath, $size = '')
    {
        $imageSizes = Config::get('image.cache-image-sizes');
        if (! isset($imageSizes[$size])) {
            $size = Config::get('image.default-current-cache-image');
        }
        $width = $imageSizes[$size]['width'];
        $height = $imageSizes[$size]['height'];
        $lifetime = (int) Config::get('image.cache-life-time', 43200);

        if (file_exists($imagePath)) {
            $cacheKey = 'image-cache:'.md5($imagePath.'|'.$width.'x'.$height);

            $encoded = Cache::remember($cacheKey, $lifetime, function () use ($imagePath, $width, $height) {
                return Image::fromPath($imagePath)->cover($width, $height)->toJpeg()->toBytes();
            });

            return response($encoded)->header('Content-Type', 'image/jpeg');
        }

        $manager = ImageManager::usingDriver(Driver::class);
        $img = $manager->createImage($width, $height)->fill('#cdcdcd');
        $img->text('image not found - 404', intval($width / 2), intval($height / 2), function ($font) {
            $font->filename(public_path('admin-assets/fonts/IRANSans/IRANSansWeb.woff'));
            $font->size(24);
            $font->color('#333333');
            $font->align('center');
            $font->valign('middle');
        });

        return response((string) $img->encodeUsingMediaType('image/jpeg'))->header('Content-Type', 'image/jpeg');
    }
}
