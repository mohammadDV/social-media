<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\FollowChangeStatusRequest;
use App\Http\Requests\SearchRequest;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface IFollowRepository.
 */
interface IFollowRepository  {

    /**
     * Get the followers and followings
     * @param User $user
     * @return array
     */
    public function index(User $user) :array;

    /**
     * Specify whether to be a follower or not.
     * @param User $user
     * @return JsonResponse
     */
    public function isFollower(User $user): array;

    /**
     * Get the followers
     * @param User $user
     * @param SearchRequest $request
     */
    public function getFollowers(User $user, SearchRequest $request);

    /**
     * Get the followings
     * @param User $user
     * @param SearchRequest $request
     */
    public function getFollowings(User $user, SearchRequest $request);

    /**
    * Store the follow
    * @param User $user
    * @return array
    */
   public function store(User $user) :array;

   /**
     * Chaneg status of the follow
     * @param User $user
     * @param FollowChangeStatusRequest $request
     * @return array
     * @throws \Exception
     */
    public function changeFollowStatus(User $user, FollowChangeStatusRequest $request) :array;

}
