<?php

namespace App\Repositories\traits;

use App\Models\Block;
use App\Models\User;
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
            throw New \Exception('Unauthorized', 403);
        }
    }

    /**
     * Check the level access
     * @param bool $conditions
     * @return bool
     */
    public function checkNickname(string $nickname, int $userId = 0) : bool {

        if (User::query()
            ->where('nickname', $nickname)
            ->when(!empty($userId), function ($query) use($userId) {
                $query->where('id', '!=', $userId);
            })
            ->count() > 0) {
                return false;
        }

        return true;
    }

    /**
     * Check the user blocked or not.
     * @param ?User $user
     * @return bool
     */
    public function isUserBlocked(?User $user) :bool
    {
        return !empty($user->id) && Block::query()
            ->where('user_id', Auth::user()->id)
            ->where('blocker_id', $user->id)
            ->count() > 0;
    }

    /**
     * Check both users blocked or not.
     * @param ?User $user
     * @return bool
     */
    public function areBothBlocked(?User $user) :bool
    {
        return !empty($user->id) && Block::query()
            ->where([
                ['user_id', $user->id],
                ['blocker_id', Auth::user()->id],
            ])
            ->orWhere([
                ['user_id', Auth::user()->id],
                ['blocker_id', $user->id],
            ])
            ->count() > 0;
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
            if(config('app.env') == "production"){
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
