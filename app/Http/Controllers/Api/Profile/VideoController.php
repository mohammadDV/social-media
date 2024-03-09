<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\TableRequest;
use App\Http\Requests\VideoFormRequest;
use App\Http\Requests\VideoRequest;
use App\Http\Requests\VideoUpdateRequest;
use App\Models\Video;
use App\Repositories\Contracts\IVideoRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VideoController extends Controller
{
    /**
     * Constructor of VideoController.
     */
    public function __construct(protected IVideoRepository $repository)
    {
        //
    }

    /**
     * Get all of video except newspaper.
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json($this->repository->index($request), Response::HTTP_OK);
    }

    /**
     * @param TableRequest $request
     * Get all of video except newspaper.
     */
    public function indexPaginate(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->indexPaginate($request), Response::HTTP_OK);
    }

    /**
     * Get the video.
     */
    public function show(Video $video): JsonResponse
    {
        return response()->json($this->repository->show($video), Response::HTTP_OK);
    }

    /**
     * @param VideoFormRequest
     * Store the video.
     */
    public function store(VideoFormRequest $request): JsonResponse
    {
        return $this->repository->store($request);
    }

    /**
     * Update the video.
     * @param VideoFormRequest $request
     * @param Video $video
     * @return JsonResponse
     */
    public function update(VideoFormRequest $request, Video $video): JsonResponse
    {
        return $this->repository->update($request, $video);
    }

    /**
     * Delete the video.
     * @param Video $video
     * @return JsonResponse
     */
    public function destroy(Video $video): JsonResponse
    {
        return $this->repository->destroy($video);
    }
}
