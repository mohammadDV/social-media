<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Models\User;
use App\Repositories\Contracts\IFollowRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    /**
     * Constructor of FollowController.
     */
    public function __construct(protected IFollowRepository $repository)
    {
        //
    }

    /**
     * Get the follow info.
     * @param ?User $user
     * @return JsonResponse
     */
    public function index(?User $user): JsonResponse
    {
        return response()->json($this->repository->index($user?->id ?? Auth::user()->id), Response::HTTP_OK);
    }

    /**
     * Get the followers.
     * @param ?User $user
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function getFollowers(?User $user, SearchRequest $request): JsonResponse
    {
        return response()->json($this->repository->getFollowers($user?->id ?? Auth::user()->id, $request), Response::HTTP_OK);
    }

    /**
     * Get the followings.
     * @param ?User $user
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function getFollowings(?User $user, SearchRequest $request): JsonResponse
    {
        return response()->json($this->repository->getFollowings($user?->id ?? Auth::user()->id, $request), Response::HTTP_OK);
    }

    /**
     * Get the followings.
     * @param User $user
     * @return JsonResponse
     */
    public function store(User $user): JsonResponse
    {
        return response()->json($this->repository->store($user), Response::HTTP_OK);
    }
}
