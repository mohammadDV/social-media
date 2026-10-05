<?php

namespace App\Services\Image;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService extends ImageToolsService
{
    public function save($image, $thumb = 0)
    {
        // set image
        $this->setImage($image);
        // execute provider
        $this->provider();

        $result = Storage::disk('s3')->put($this->getFinalImageDirectory(), $image, 'public');

        $url = Storage::disk('s3')->url($result);
        $path = parse_url($url, PHP_URL_PATH);

        if (! empty($thumb)) {
            $fileName = '/thumbnails/'.basename($path);
            $resizedImage = Image::fromUpload($image)->scale(width: 150, height: 100)->toJpeg()->toBytes();
            Storage::disk('s3')->put($this->getFinalImageDirectory().$fileName, $resizedImage, 'public');

            $fileName = '/slides/'.basename($path);
            $resizedImage = Image::fromUpload($image)->scale(width: 455, height: 303)->toJpeg()->toBytes();
            Storage::disk('s3')->put($this->getFinalImageDirectory().$fileName, $resizedImage, 'public');
        }

        return str_replace('prod-data-sport.storage.iran.liara.space', 'cdn.varzeshpod.com', Storage::disk('s3')->url($result));
    }

    public function fitAndSave($image, $width, $height)
    {
        // set image
        $this->setImage($image);
        // execute provider
        $this->provider();
        // save image
        $bytes = Image::fromUpload($image)->cover($width, $height)->toBytes();
        $result = file_put_contents(public_path($this->getImageAddress()), $bytes);

        return $result ? $this->getImageAddress() : false;
    }

    public function createIndexAndSave($image)
    {
        // get data from config
        $imageSizes = Config::get('image.index-image-sizes');

        // set image
        $this->setImage($image);

        // set directory
        $this->getImageDirectory() ?? $this->setImageDirectory(date('Y').DIRECTORY_SEPARATOR.date('m').DIRECTORY_SEPARATOR.date('d'));
        $this->setImageDirectory($this->getImageDirectory().DIRECTORY_SEPARATOR.time().rand(1111, 99999));

        // set name
        $this->getImageName() ?? $this->setImageName(Str::uuid());
        $imageName = $this->getImageName();

        $indexArray = [];
        foreach ($imageSizes as $sizeAlias => $imageSize) {

            // create and set this size name
            $currentImageName = $imageName.'_'.$sizeAlias;
            $this->setImageName($currentImageName);

            // execute provider
            $this->provider();

            // save image
            $bytes = Image::fromUpload($image)
                ->cover($imageSize['width'], $imageSize['height'])
                ->toBytes();
            $result = file_put_contents(public_path($this->getImageAddress()), $bytes);
            if ($result) {
                $indexArray[$sizeAlias] = $this->getImageAddress();
            } else {
                return false;
            }

        }
        $images['indexArray'] = $indexArray;
        $images['directory'] = $this->getFinalImageDirectory();
        $images['currentImage'] = Config::get('image.default-current-index-image');

        return $images;
    }

    public function deleteImage($imagePath)
    {
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    public function deleteIndex($images)
    {
        $directory = public_path($images['directory']);
        $this->deleteDirectoryAndFiles($directory);
    }

    public function deleteDirectoryAndFiles($directory)
    {
        if (! is_dir($directory)) {
            return false;
        }

        $files = glob($directory.DIRECTORY_SEPARATOR.'*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->deleteDirectoryAndFiles($file);
            } else {
                unlink($file);
            }
        }

        $result = rmdir($directory);

        return $result;
    }
}
