<?php

namespace App\Repositories\Contracts;

 /**
 * Interface IPostRepository.
 */
interface IPostRepository  {

    /**
     * Get the posts.
     * @param $categories
     * @param $count
     * @return array
     */
    public function index(array $categories, int $count) :array;

}
