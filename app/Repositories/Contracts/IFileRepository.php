<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\FileRequest;

 /**
 * Interface IFileRepository.
 */
interface IFileRepository  {

    /**
     * Upload the file
     * @param FileRequest $request
     * @return array
     */
    public function uploadFile(FileRequest $request);

}
