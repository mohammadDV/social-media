<?php

namespace App\Repositories\traits;

use App\Services\Image\ImageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

trait GlobalFunc
{
    /**
     * Check the level access
     * @param bool $conditions
     * @return void
     */
    public function checkLevelAccess(bool $condition = false) {

        if (!$condition && Auth::user()->level != 3) {
            throw New \Exception('Unauthorized', 401);
        }
    }

    /**
     * Check the level access
     * @param ImageService $imageService
     * @param $file
     * @param string $url
     * @param string $image
     * @return void
     */
    public function uploadImage(ImageService $imageService, $file,string $url, $image){
        $imageService->setExclusiveDirectory($url);
        $result = $imageService->save($file);
        if ($result && !empty($image)){
            if(env('APP_ENV') == "production"){
                Storage::disk('s3')->delete($image);
            }else{
                $imageService->deleteImage($image);
            }
        }
        $imageService->reset();

        return $result;
    }
}
;
