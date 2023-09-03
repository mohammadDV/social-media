<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface IStatusRepository.
 */
interface IStatusRepository  {

    /**
     * Get the status.
     * @param ?User $user
     * @return LengthAwarePaginator
     */
    public function index(?User $user) :LengthAwarePaginator;

}
