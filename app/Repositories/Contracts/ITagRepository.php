<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

 /**
 * Interface ITagRepository.
 */
interface ITagRepository  {

    /**
     * Get the random tags.
     * @return Collection
     */
    public function getRandom() :Collection;

}
