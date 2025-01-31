<?php

namespace App\Repositories;

use App\Http\Requests\FileRequest;
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
            $imageResult = $this->imageService->save($request->file('image'),
            !empty($request->input('thumb')) ? 1 : 0
        );
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

    /**
     * Upload the video
     * @param FileRequest $request
     * @return array
     */
    public function uploadFile(FileRequest $request)
    {

        if ($request->hasFile('file')) {

            $this->fileService->setExclusiveDirectory('uploads' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $request->input('dir', 'default'));
            $fileResult = $this->fileService->moveToStorage($request->file('file'));

            if (!$fileResult){
                throw new \Exception(__('site.Error in save data'));
            }

            return [
                'status' => !empty($fileResult),
                'url' => $fileResult
            ];
        }

        return [
            'status' => false,
            'url' => ''
        ];

    }
}
