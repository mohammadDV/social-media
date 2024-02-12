<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileRequest;
use App\Http\Requests\ImageRequest;
use App\Http\Requests\VideoRequest;
use App\Repositories\Contracts\IFileRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class FileController extends Controller
{
    /**
     * Constructor of ILiveRepository.
     */
    public function __construct(protected IFileRepository $repository)
    {
        //
    }

    /**
     * Upload the image.
     * @param ImageRequest $request
     */
    public function uploadImage(ImageRequest $request): JsonResponse
    {
        return response()->json($this->repository->uploadImage($request), Response::HTTP_OK);
    }

    /**
     * Upload the video.
     * @param VideoRequest $request
     */
    public function uploadVideo(VideoRequest $request): JsonResponse
    {
        return response()->json($this->repository->uploadVideo($request), Response::HTTP_OK);
    }
    /**
     * Upload the video.
     * @param FileRequest $request
     */
    public function uploadFile(FileRequest $request): JsonResponse
    {
        return response()->json($this->repository->uploadFile($request), Response::HTTP_OK);
    }

}
