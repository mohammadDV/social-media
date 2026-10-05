<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\FollowChangeStatusRequest;
use App\Http\Requests\SearchRequest;
use App\Models\User;

/**
 * Interface IFollowRepository.
 */
interface IFollowRepository
{
    /**
     * Get the followers and followings
     */
    public function index(User $user): array;

    /**
     * Specify whether to be a follower or not.
     *
     * @return JsonResponse
     */
    public function isFollower(User $user): array;

    /**
     * Get the followers
     */
    public function getFollowers(User $user, SearchRequest $request);

    /**
     * Get the followings
     */
    public function getFollowings(User $user, SearchRequest $request);

    /**
     * Store the follow
     */
    public function store(User $user): array;

    /**
     * Chaneg status of the follow
     *
     * @throws \Exception
     */
    public function changeFollowStatus(User $user, FollowChangeStatusRequest $request): array;
}
