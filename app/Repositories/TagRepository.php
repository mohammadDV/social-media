<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Repositories\Contracts\ITagRepository;
use Illuminate\Database\Eloquent\Collection;

class TagRepository implements ITagRepository {

    /**
     * Get the random tags.
     * @return Collection
     */
    public function getRandom() :Collection
    {
        return cache()->remember("tags.random", now(), function () {
            return Tag::query()
                ->inRandomOrder()
                ->limit(15)
                ->get();
        });
    }
}
