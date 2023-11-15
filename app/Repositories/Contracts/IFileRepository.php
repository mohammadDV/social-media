<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\ImageRequest;
use App\Http\Requests\VideoRequest;

 /**
 * Interface IFileRepository.
 */
interface IFileRepository  {

    /**
     * Upload the image
     * @param ImageRequest $request
     * @return array
     */
    public function uploadImage(ImageRequest $request);

    /**
     * Upload the video
     * @param VideoRequest $request
     * @return array
     */
    public function uploadVideo(VideoRequest $request);

}
