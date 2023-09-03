<?php

namespace App\Repositories;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Repositories\Contracts\ICategoryRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryRepository implements ICategoryRepository {

    /**
     * Get the active categories.
     * @return AnonymousResourceCollection
     */
    public function getActıves() :AnonymousResourceCollection
    {
        // ->addMinutes('1'),
        return cache()->remember("categories.all", now(), function () {
            return CategoryResource::collection(Category::query()
                ->where('status',1)->get());
        });
    }
}
