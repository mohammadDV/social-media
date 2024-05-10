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
    public function getActives() :AnonymousResourceCollection
    {
        // ->addMinutes('1'),
        return cache()->remember("categories.all", now(), function () {
            return CategoryResource::collection(Category::query()
                ->where('status',1)
                ->where('menu',1)
                ->get());
        });
    }

    /**
     * Get the team categories.
     * @return AnonymousResourceCollection
     */
    public function getTeamCategories() :AnonymousResourceCollection
    {
        return cache()->remember("categories.all", now(), function () {
            return CategoryResource::collection(Category::query()
                ->where('status',1)
                ->where('menu',0)
                ->get());
        });
    }

    /**
     * Get all.
     * @return AnonymousResourceCollection
     */
    public function index() :AnonymousResourceCollection
    {
        return CategoryResource::collection(Category::all());
    }
}
