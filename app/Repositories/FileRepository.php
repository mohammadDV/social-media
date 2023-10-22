<?php

namespace App\Repositories;

use App\Http\Requests\FileRequest;
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
     * Upload the file
     * @param FileRequest $request
     * @return array
     */
    public function uploadFile(FileRequest $request)
    {

        if ($request->hasFile('image')) {
            $this->imageService->setExclusiveDirectory('uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $request->input('dir', 'default'));
            $imageResult = $this->imageService->save($request->file('image'));
            if (!$imageResult){
                throw new \Exception(__('site.Error in save data'));
            }

            // if ($imageResult && !empty($advertise->image)){
            //     $imageService->deleteImage($advertise->image);
            // }

            return [
                'status' => !empty($imageResult),
                'url' => $imageResult
            ];
        }

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
