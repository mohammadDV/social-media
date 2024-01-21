<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\TableRequest;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface IRpcRepository.
 */
interface IRpcRepository  {

    /**
     * Get the necessary thing for user.
     * @return array
     */
    public function index() :array;

}
