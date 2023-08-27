<?php

namespace App\Repositories\Contracts;

 /**
 * Interface ILiveRepository.
 */
interface ILiveRepository  {

    /**
     * Get the lives.
     * @return array
     */
    public function index() :array;

}
