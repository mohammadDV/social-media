<?php

namespace App\Repositories;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\Tag;
use App\Repositories\Contracts\ITagRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TagRepository implements ITagRepository {

    /**
     * Get the random tags.
     * @return Collection
     */
    public function getRandom() :Collection
    {
        return cache()->remember("tags.random", now()->addMinutes(10), function () {
            return Tag::query()
                ->inRandomOrder()
                ->limit(15)
                ->get();
        });
    }

    /**
     * Get all contents that has this tag
     * @param Tag $tag
     * @return AnonymousResourceCollection
     */
    public function index(Tag $tag) :AnonymousResourceCollection
    {
        $page = !empty(request()->page) ? request()->page : 1;
        $posts = cache()->remember("site.tags.index." . $tag->id . "." . $page, now()->addMinute(config('default_min')),
            function () use($tag) {
                return Post::query()
                    ->with('tags')
                    ->where('status', '=', 1)
                    ->whereHas('tags', function ($query) use ($tag) {
                        $query->where('id', $tag->id);
                    })
                    ->orderBy('id', 'DESC')
                    ->paginate(20);
             });

        return PostResource::collection($posts);

    }
}
