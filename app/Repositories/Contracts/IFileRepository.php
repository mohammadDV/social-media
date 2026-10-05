<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\FileRequest;
use App\Http\Requests\ImageRequest;
use App\Http\Requests\VideoRequest;

/**
 * Interface IFileRepository.
 */
interface IFileRepository
{
    /**
     * Upload the image
     *
     * @return array
     */
    public function uploadImage(ImageRequest $request);

    /**
     * Upload the video
     *
     * @return array
     */
    public function uploadVideo(VideoRequest $request);

    /**
     * Upload the file
     *
     * @return array
     */
    public function uploadFile(FileRequest $request);
}
