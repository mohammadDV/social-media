<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\TableRequest;
use App\Http\Requests\VideoFormRequest;
use App\Models\Video;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface IVideoRepository.
 */
interface IVideoRepository
{
    /**
     * Get all active videos.
     */
    public function index(): Collection;

    /**
     * Get the video.
     *
     * @return array
     */
    public function show(Video $video);

    /**
     * Get the video pagination.
     */
    public function indexPaginate(TableRequest $request): LengthAwarePaginator;

    /**
     * Store the video.
     */
    public function store(VideoFormRequest $request): JsonResponse;

    /**
     * Update the video.
     *
     * @throws \Exception
     */
    public function update(VideoFormRequest $request, Video $video): JsonResponse;

    /**
     * Delete the video.
     *
     * @throws \Exception
     */
    public function destroy(Video $video): JsonResponse;
}
