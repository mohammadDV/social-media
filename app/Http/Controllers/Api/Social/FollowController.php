<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Http\Requests\FollowChangeStatusRequest;
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
     */
    public function index(?User $user): JsonResponse
    {
        return response()->json($this->repository->index($user?->id ? $user : Auth::user()), Response::HTTP_OK);
    }

    /**
     * Specify whether to be a follower or not.
     */
    public function isFollower(User $user): JsonResponse
    {
        return response()->json($this->repository->isFollower($user), Response::HTTP_OK);
    }

    /**
     * Get the followers.
     */
    public function getFollowers(?User $user, SearchRequest $request): JsonResponse
    {
        return response()->json($this->repository->getFollowers($user?->id ? $user : Auth::user(), $request), Response::HTTP_OK);
    }

    /**
     * Get the followings.
     */
    public function getFollowings(?User $user, SearchRequest $request): JsonResponse
    {
        return response()->json($this->repository->getFollowings($user?->id ? $user : Auth::user(), $request), Response::HTTP_OK);
    }

    /**
     * Store the follow.
     */
    public function store(User $user): JsonResponse
    {
        return response()->json($this->repository->store($user), Response::HTTP_OK);
    }

    /**
     * Change status of the follow.
     */
    public function changeFollowStatus(User $user, FollowChangeStatusRequest $request): JsonResponse
    {
        return response()->json($this->repository->changeFollowStatus($user, $request), Response::HTTP_OK);
    }
}
