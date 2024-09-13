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

    /**
     * Get the poular categories.
     * @return AnonymousResourceCollection
     */
    public function popularCategories() :AnonymousResourceCollection;

    /**
     * Get the team categories.
     * @return AnonymousResourceCollection
     */
    public function getTeamCategories() :AnonymousResourceCollection;

    /**
     * Get all.
     * @return AnonymousResourceCollection
     */
    public function index() :AnonymousResourceCollection;

}
