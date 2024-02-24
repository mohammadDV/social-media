<?php

namespace App\Repositories\Contracts;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

 /**
 * Interface ITagRepository.
 */
interface ITagRepository  {

    /**
     * Get the random tags.
     * @return Collection
     */
    public function getRandom() :Collection;

     /**
     * Get all contents that has this tag
     * @param Tag $tag
     */
    public function index(Tag $tag) :AnonymousResourceCollection;

}
