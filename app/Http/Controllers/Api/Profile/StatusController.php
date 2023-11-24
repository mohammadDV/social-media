<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use App\Http\Requests\StatusUpdateRequest;
use App\Models\Status;
use App\Repositories\Contracts\IStatusRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StatusController extends Controller
{
    /**
     * Constructor of StatusController.
     */
    public function __construct(protected IStatusRepository $repository)
    {
        //
    }

    /**
     * Get all of post except newspaper.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json($this->repository->statusPaginate($request), Response::HTTP_OK);
    }

    /**
     * Store the post.
     * @param StatusRequest $request
     * @return JsonResponse
     */
    public function store(StatusRequest $request): JsonResponse
    {
        return $this->repository->store($request);
    }

    /**
     * Update the post.
     * @param StatusUpdateRequest $request
     * @param Status $status
     * @return JsonResponse
     */
    public function update(StatusUpdateRequest $request, Status $status): JsonResponse
    {
        return $this->repository->update($request, $status);
    }

    /**
     * Delete the post.
     * @param Post $post
     * @return JsonResponse
     */
    public function destroy(Status $status): JsonResponse
    {
        return $this->repository->destroy($status);
    }

    /**
     * Delete completely the post.
     * @param int $id
     * @return JsonResponse
     */
    public function realDestroy(int $id): JsonResponse
    {
        return $this->repository->realDestroy($id);
    }
}
