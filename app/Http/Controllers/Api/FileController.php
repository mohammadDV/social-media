<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileRequest;
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
     * Get all of lives.
     */
    public function uploadFile(FileRequest $request): JsonResponse
    {
        return response()->json($this->repository->uploadFile($request), Response::HTTP_OK);
    }

}
