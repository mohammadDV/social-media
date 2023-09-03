<?php

namespace App\Repositories\Contracts;

 /**
 * Interface IAdvertiseRepository.
 */
interface IAdvertiseRepository  {

   /**
     * Get the places.
     * @param array %places
     * @return array
     */
    public function index(array $places) : array;

}
