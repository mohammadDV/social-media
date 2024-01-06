<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

 /**
 * Interface ILikeRepository.
 */
interface ILikeRepository  {

    /**
     * Get the all likes
     * @param int $id
     * @param string $type
     * @return Collection
     */
    public function getLikes(int $id, string $type) :Collection;

    /**
     * Get the like
     * @param int $id
     * @param string $type
     * @return JsonResponse
     */
    public function store(int $id, string $type) :JsonResponse;

    /**
     * Get the count of likes
     * @param int $id
     * @param string $type
     * @return int
     */
    public function getCount(int $id, string $type) :int;


}
