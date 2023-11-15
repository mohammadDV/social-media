<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

 /**
 * Interface ICategoryRepository.
 */
interface ICategoryRepository  {

    /**
     * Get the active categories.
     * @return AnonymousResourceCollection
     */
    public function getActives() :AnonymousResourceCollection;

}
