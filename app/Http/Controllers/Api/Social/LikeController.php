<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Http\Requests\LikeStoreRequest;
use App\Repositories\Contracts\ILikeRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LikeController extends Controller
{
    /**
     * Constructor of PostController.
     */
    public function __construct(protected ILikeRepository $repository)
    {
        //
    }

    /**
     * Get the like of the entity
     * @param LikeStoreRequest $request
     * @return JsonResponse
     */
    public function getLikes(LikeStoreRequest $request): JsonResponse
    {
        return response()->json($this->repository->getLikes($request->id, $request->type), Response::HTTP_OK);
    }

    /**
     * Get the count of like of the entity
     * @param LikeStoreRequest $request
     * @return JsonResponse
     */
    public function getLikeCount(LikeStoreRequest $request): JsonResponse
    {
        return response()->json([
            'count' => $this->repository->getCount($request->id, $request->type)
        ], Response::HTTP_OK);
    }

    /**
     * Store like of the entity
     * @param LikeStoreRequest $request
     * @return JsonResponse
     */
    public function store(LikeStoreRequest $request): JsonResponse
    {
        return $this->repository->store($request->id, $request->type);
    }
}
