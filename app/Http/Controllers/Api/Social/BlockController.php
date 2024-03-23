<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Models\User;
use App\Repositories\Contracts\IBlockRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class BlockController extends Controller
{
    /**
     * Constructor of BlockController.
     */
    public function __construct(protected IBlockRepository $repository)
    {
        //
    }

    /**
     * Get the block users.
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request): JsonResponse
    {
        return response()->json($this->repository->index($request), Response::HTTP_OK);
    }

    /**
     * Store the block.
     * @param User $user
     * @return JsonResponse
     */
    public function store(User $user): JsonResponse
    {
        return response()->json($this->repository->store($user), Response::HTTP_OK);
    }
}
