<?php

namespace App\Repositories\Contracts;


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
