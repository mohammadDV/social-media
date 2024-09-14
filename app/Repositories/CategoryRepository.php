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
        return cache()->remember("categories.all.actives", now()->addMinutes(10), function () {
            return CategoryResource::collection(Category::query()
                ->where('status',1)
                ->where('menu',1)
                ->get());
        });
    }


    /**
     * Get the popular categories.
     * @return AnonymousResourceCollection
     */
    public function popularCategories() :AnonymousResourceCollection
    {
        return cache()->remember("categories.pupular", now()->addMinutes(10), function () {
            return CategoryResource::collection(Category::query()
                ->withCount('posts')
                ->where('status',1)
                ->where('menu', 0)
                ->take(50)
                ->orderby('posts_count', 'DESC')
                ->orderby('alias_title', 'DESC')
                ->get());
        });
    }

    /**
     * Get the team categories.
     * @return AnonymousResourceCollection
     */
    public function getTeamCategories() :AnonymousResourceCollection
    {
        return cache()->remember("categories.all.team", now()->addMinutes(config('cache.default_min')), function () {
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
