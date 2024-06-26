<?php

namespace App\Repositories;

use App\Http\Requests\PostRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Repositories\Contracts\IPostRepository;
use App\Repositories\traits\GlobalFunc;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostRepository implements IPostRepository {

    use GlobalFunc;

    protected $categories_id    = [];
    protected $count            = 100;
    protected $latestCount      = 50;
    protected $spVideoCount     = 10;
    protected $categoryCount    = 20;
    protected $spPostCount      = 5;

    /**
     * Get the post.
     * @param Post $post
     * @return array
     */
    public function show(Post $post) {

        $this->checkLevelAccess($post->user_id == Auth::user()->id);

        return Post::query()
            ->where('id', $post->id)
            ->with('tags', 'categories')
            ->first();
    }

    /**
     * Get the posts.
     * @param $categories
     * @param $count
     * @return array
     */
    public function index(array $categoryIds, int $count) :array
    {
        // ->addMinutes('1'),
        $posts = cache()->remember("post.all." . implode(".",$categoryIds) . "." . $count, now(),
            function () use($categoryIds, $count) {
                return Post::query()
                ->with('categories')
                ->where('status', 1)
                ->whereHas('categories', function ($query) use ($categoryIds) {
                    $query->whereIn('id', $categoryIds);
                })
                ->latest()->take($count)->get();
            }
        );

        $data['posts']          = [];
        $data['videos']         = [];
        $data['specialVideos']  = [];
        $data['specialPosts']   = [];
        $data['latest']         = [];
        $data['challenged']     = $this->getChallenged();
        $data['popular']        = $this->getPopular();
        foreach($posts ?? [] as $post){
            count($data['latest'])  >= $this->latestCount ?: $data['latest'][] =  $post;
            if($post->type === 1) {
                if($post->special === 1 && count($data['specialVideos']) < $this->spVideoCount) { $data['specialVideos'][] = $post; }
                $data['videos'][] = $post;
            } else {
                if($post->special === 1 && count($data['specialPosts']) < $this->spPostCount) {
                    $data['specialPosts'][] = $post;
                }

                foreach ($post->categories ?? [] as $category) {

                    if (in_array($category->id, $categoryIds) ) {
                        $data['posts'][$category->id][] = $post;
                    }
                }

            }
        }

        // Get special posts
        if (count($data['specialPosts']) < $this->spPostCount) {
            $data['specialPosts'] = $this->getSpecialPosts($categoryIds);
        }

        // Get special videos
        if (count($data['specialVideos']) < $this->spVideoCount) {
            $data['specialVideos'] = $this->getSpecialVideos();
        }

        foreach ($categoryIds as $categoryId) {
            if (empty($data['posts'][$categoryId]) || count($data['posts'][$categoryId]) < $this->categoryCount) {
                $data['posts'][$categoryId] = Post::query()
                    ->where('status', 1)
                    ->whereHas('categories', function ($query) use ($categoryId) {
                        $query->where('id', $categoryId);
                    })
                    ->latest()
                    ->take($this->categoryCount)
                    ->get();
            }
        }

        return $data;

    }

    /**
     * Get special videos
     * @return Collection
     */
    private function getSpecialVideos() : Collection
    {
        return Post::query()
                ->where('status',1)
                ->where('type',1)
                ->where('special',1)
                ->latest()
                ->take($this->spVideoCount)
                ->get();
    }

    /**
     * Get special posts
     * @return Collection
     */
    private function getSpecialPosts(array $categoryIds) : Collection
    {
        return Post::query()
                ->where('status',1)
                ->whereHas('categories', function ($query) use ($categoryIds) {
                    $query->whereIn('id', $categoryIds);
                })
                ->latest()
                ->take($this->spPostCount)
                ->get();
    }

    /**
     * Get the popular posts.
     * @return array
     */
    private function getPopular() : object
    {
        return Post::query()
            ->where('status',1)
            ->orderBy('view','DESC')
            ->take($this->latestCount)
            ->get();
    }

    /**
     * Get the challenged posts.
     * @return array
     */
    private function getChallenged() : object
    {

        return Post::query()
            ->where('status', 1)
            ->whereHas('comments')
            ->withCount('comments')
            ->orderBy('comments_count', 'desc')
            ->limit($this->latestCount)
            ->get();

    }

     /**
     * Get the post info.
     * @param Post $post
     * @return PostResource
     */
    public function getPostInfo(Post $post) :PostResource
    {

        $post->increment('view');

        $post = Post::query()
            ->with('tags', 'categories' , 'comments.user', 'comments.parents', 'advertise')
            ->find($post->id);
        return new PostResource($post);
    }

    /**
     * Get all of post per category.
     * @param Category $category
     * @return array
     */
    public function getPostsPerCategory(Category $category) :array
    {
        // ->addMinutes('1'),
        $data = cache()->remember("posts.per.category." . $category->id, now(),
            function () use($category) {
                $posts = Post::query()
                    ->whereHas('categories', function ($query) use ($category) {
                        $query->where('id', $category->id);
                    })
                    ->where('status',1)
                    ->orderBy('id', 'desc')
                    ->paginate(10);

                return PostResource::collection($posts);
        });

        return [
            'data' => $data,
            'category' => $category
        ];
    }

    /**
     * Get searched posts.
     * @param search $category
     * @return AnonymousResourceCollection
     */
    public function search(string $search) :AnonymousResourceCollection
    {
        // ->addMinutes('1'),
        $posts = Post::where('status', '=', 1)
        ->where(function ($query) use ($search) {
            $query->where('title', "like", "%" . $search . "%");
            $query->orWhere('pre_title', "like", "%" . $search . "%");
            $query->orWhere('content', "like", "%" . $search . "%");
            $query->orWhere('summary', "like", "%" . $search . "%");
        })->orderBy('id', 'DESC')->paginate(10);

        return PostResource::collection($posts);

    }

    /**
     * Get searched posts.
     * @param search $category
     * @return array
     */
    public function searchPostTag(string $search) :array
    {
        $data['posts'] = PostResource::collection(Post::query()
            ->where('status', '=', 1)
            ->where(function ($query) use ($search) {
                $query->where('title', "like", "%" . $search . "%");
                $query->orWhere('pre_title', "like", "%" . $search . "%");
                $query->orWhere('content', "like", "%" . $search . "%");
                $query->orWhere('summary', "like", "%" . $search . "%");
            })
            ->orderBy('id', 'DESC')
            ->limit(6)
            ->get());

        $data['tags'] = Tag::query()
            ->where(function ($query) use ($search) {
                $query->where('title', "like", "%" . $search . "%");
            })
            ->orderBy('id', 'DESC')
            ->limit(10)
            ->get();

        return $data;

    }

    /**
     * Get all posts.
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function postPaginate(Request $request) :LengthAwarePaginator
    {

        $search = $request->get('query');
        return Post::query()
            ->when(Auth::user()->level != 3, function ($query) {
                return $query->where('user_id', Auth::user()->id);
            })
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where('title', 'like', '%' . $search . '%');
            })
            ->withCount('comments')
            ->orderBy($request->get('sortBy', 'id'), $request->get('sortType', 'desc'))
            ->paginate($request->get('rowsPerPage', 25));
    }

    /**
     * Store the post.
     *
     * @param  PostRequest  $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(PostRequest $request) :JsonResponse
    {


        $post = auth()->user()->posts()->create([
            'pre_title'   => $request->input('pre_title'),
            'title'       => $request->input('title'),
            'content'     => $request->input('content'),
            'summary'     => $request->input('summary'),
            'image'       => $request->input('image', null),
            'video'       => $request->input('type') == 1 ? $request->input('video', null) : null,
            'video_id'       => $request->input('type') == 1 ? $request->input('video_id') : null,
            'type'        => $request->input('type',0),
            'status'      => $request->input('status'),
            'special'     => $request->input('special',0),
        ]);

        $post->categories()->sync($request->input('categories'));

        if (!empty($request->input('tags')) && is_array($request->input('tags'))) {
            $tagIds = [];
            $tags_arr = array_unique($request->input('tags'));
            if (!empty($tags_arr)){
                foreach($tags_arr as $tagitem) {
                    $tagIds[] = Tag::firstOrCreate(['title' => $tagitem])->id;
                }
                $post->tags()->attach($tagIds);
            }
        }


        return response()->json([
            'status' => 1,
            'message' => __('site.New post has been stored')
        ], 200);
    }

    /**
     * Update the post.
     *
     * @param  PostUpdateRequest  $request
     * @param  Post  $post
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(PostUpdateRequest $request, Post $post) :JsonResponse
    {

        $this->checkLevelAccess($post->user_id == Auth::user()->id);

        DB::beginTransaction();
        try {
            $post->update([
                'pre_title'   => $request->input('pre_title'),
                'title'       => $request->input('title'),
                'content'     => $request->input('content'),
                'summary'     => $request->input('summary'),
                'image'       => $request->input('image', null),
                'video'       => $request->input('type') == 1 ? $request->input('video', null) : null,
                'video_id'    => $request->input('type') == 1 ? $request->input('video_id') : null,
                'type'        => $request->input('type',0),
                'status'      => $request->input('status'),
            ]);

            $post->categories()->sync($request->input('categories'));

            if (is_array($request->input('tags'))) {
                $tagIds = [];
                $tags_arr = !empty($request->input('tags')) ? array_unique($request->input('tags')) : [];
                if (!empty($tags_arr)){
                    foreach($tags_arr as $tagitem) {
                        if (!empty($tagitem)){
                            $tagIds[] = Tag::firstOrCreate(['title' => $tagitem])->id;
                        }
                    }
                }
                $post->tags()->sync($tagIds);
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception(__('site.Error in save data'));
        }

        return response()->json([
            'status' => 1,
            'message' => __('site.The post has been updated')
        ], 200);
    }

    /**
     * Delete the post.
     *
     * @param  Post  $post
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(Post $post) :JsonResponse
    {

        $this->checkLevelAccess($post->user_id == Auth::user()->id);

        $post->delete();

        return response()->json([
            'status' => 1,
            'message' => __('site.The post has been deleted')
        ], 200);
    }

    /**
     * Delete completely the post.
     * @param int $id
     * @return JsonResponse
     */
    public function realDestroy(int $id): JsonResponse
    {

        $this->checkLevelAccess();

        try {
            DB::beginTransaction();

            $post = Post::withTrashed()->where('id', $id)->first();

            if (!empty($post->image)){
                $this->imageService->deleteDirectoryAndFiles($post->image['directory']);
            }
            if (!empty($post->video)){
                $this->fileService->deleteFile($post->video);
            }

            $post->tags()->detach();
            $delete = $post->forceDelete();
            if ($delete){
                DB::commit();
                return response()->json([
                    'status' => 1,
                    'message' => __('site.The post has been deleted')
                ], 200);
            }
        }catch (\Exception $e){
            DB::rollBack();
            throw new \Exception(__('site.Error in save data'));
        }

        throw new \Exception(__('site.Error in save data'));
    }
}
