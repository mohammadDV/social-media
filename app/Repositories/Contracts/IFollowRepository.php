<?php

namespace App\Repositories\Contracts;

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
     * @return LengthAwarePaginator
     */
    public function getFollowers(int $userId) :LengthAwarePaginator;

    /**
     * Get the followings
     * @param int $userId
     * @return LengthAwarePaginator
     */
    public function getFollowings(int $userId) :LengthAwarePaginator;/**

    * Store the follow
    * @param User $user
    * @return array
    */
   public function store(User $user) :array;

}
