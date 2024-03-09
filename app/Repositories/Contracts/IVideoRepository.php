<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\TableRequest;
use App\Http\Requests\VideoFormRequest;
use App\Http\Requests\VideoRequest;
use App\Http\Requests\VideoUpdateRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface IVideoRepository.
 */
interface IVideoRepository  {

    /**
     * Get all active videos.
     * @return Collection
     */
    public function index() :Collection;

    /**
     * Get the video.
     * @param Video $video
     * @return array
     */
    public function show(Video $video);

    /**
     * Get the video pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator;

    /**
     * Store the video.
     *
     * @param  VideoFormRequest  $request
     * @return JsonResponse
     */
    public function store(VideoFormRequest $request) :JsonResponse;

    /**
     * Update the video.
     *
     * @param  VideoFormRequest  $request
     * @param  Video  $video
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(VideoFormRequest $request, Video $video) :JsonResponse;

    /**
     * Delete the video.
     *
     * @param  Video  $video
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(Video $video) :JsonResponse;

}
