<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\SearchRequest;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface IFollowRepository.
 */
interface IFollowRepository  {

    /**
     * Get the followers and followings
     * @param int $userId
     * @return array
     */
    public function index(int $userId) :array;

    /**
     * Get the followers
     * @param int $userId
     * @param SearchRequest $request
     * @return LengthAwarePaginator
     */
    public function getFollowers(int $userId, SearchRequest $request) :LengthAwarePaginator;

    /**
     * Get the followings
     * @param int $userId
     * @param SearchRequest $request
     * @return LengthAwarePaginator
     */
    public function getFollowings(int $userId, SearchRequest $request) :LengthAwarePaginator;/**

    * Store the follow
    * @param User $user
    * @return array
    */
   public function store(User $user) :array;

}
