<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\LiveRequest;
use App\Http\Requests\TableRequest;
use App\Models\Live;
use App\Repositories\Contracts\ILiveRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LiveController extends Controller
{
    /**
     * Constructor of LiveController.
     */
    public function __construct(protected  ILiveRepository $repository)
    {
        //
    }

    /**
     * Get all of lives with pagination
     * @param TableRequest $request
     * @return JsonResponse
     */
    public function indexPaginate(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->indexPaginate($request), Response::HTTP_OK);
    }

    /**
     * Get all of lives
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json($this->repository->index(), Response::HTTP_OK);
    }

    /**
     * Get the live.
     * @param
     * @return JsonResponse
     */
    public function show(Live $live) :JsonResponse
    {
        return response()->json($this->repository->show($live), Response::HTTP_OK);
    }

    /**
     * Store the live.
     * @param LiveRequest $request
     * @return JsonResponse
     */
    public function store(LiveRequest $request) :JsonResponse
    {
        return $this->repository->store($request);
    }

    /**
     * Update the live.
     * @param LiveUpdateRequest $request
     * @param Live $live
     * @return JsonResponse
     */
    public function update(LiveRequest $request, Live $live) :JsonResponse
    {
        return $this->repository->update($request, $live);
    }

    /**
     * Delete the live.
     * @param Live $live
     * @return JsonResponse
     */
    public function destroy(Live $live) :JsonResponse
    {
        return $this->repository->destroy($live);
    }
}
