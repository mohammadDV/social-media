<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\SearchRequest;
use App\Models\User;

 /**
 * Interface IBlockRepository.
 */
interface IBlockRepository  {

    /**
     * Get the blocks users
     * @param SearchRequest $request
     */
    public function index(SearchRequest $request);

    /**
    * Store the block
    * @param User $user
    * @return array
    */
   public function store(User $user) :array;

}
