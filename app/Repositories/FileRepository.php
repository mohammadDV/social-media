<?php

namespace App\Repositories;

use App\Http\Requests\ImageRequest;
use App\Http\Requests\VideoRequest;
use App\Repositories\Contracts\IFileRepository;
use App\Services\File\FileService;
use App\Services\Image\ImageService;

class FileRepository implements IFileRepository {

    /**
     * @param ImageService $imageService
     * @param FileService $fileService
     */
    public function __construct(protected ImageService $imageService, protected FileService $fileService)
    {

    }

    /**
     * Upload the image
     * @param ImageRequest $request
     * @return array
     */
    public function uploadImage(ImageRequest $request)
    {
        if ($request->hasFile('image')) {
            $this->imageService->setExclusiveDirectory('uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $request->input('dir', 'default'));
            $imageResult = $this->imageService->save($request->file('image'));
            if (!$imageResult){
                throw new \Exception(__('site.Error in save data'));
            }

            return [
                'status' => !empty($imageResult),
                'url' => $imageResult
            ];
        }

        return [
            'status' => false,
            'url' => ''
        ];

    }

    /**
     * Upload the video
     * @param VideoRequest $request
     * @return array
     */
    public function uploadVideo(VideoRequest $request)
    {

        if ($request->hasFile('video')) {

            $this->fileService->setExclusiveDirectory('uploads' . DIRECTORY_SEPARATOR . 'videos' . DIRECTORY_SEPARATOR . $request->input('dir', 'default'));
            $videoResult = $this->fileService->moveToStorage($request->file('video'));

            if (!$videoResult){
                throw new \Exception(__('site.Error in save data'));
            }

            return [
                'status' => !empty($videoResult),
                'url' => $videoResult
            ];
        }

        return [
            'status' => false,
            'url' => ''
        ];

    }
}
