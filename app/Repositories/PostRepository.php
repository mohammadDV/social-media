<?php

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\Contracts\IPostRepository;

class PostRepository implements IPostRepository {

    protected $categories_id    = [];
    protected $count            = 100;
    protected $latestCount      = 50;
    protected $spVideoCount     = 10;
    protected $spPostCount      = 5;
    protected $ignoreCategories = [7]; // 1 = writers,  5 = Football analysis , 6 = Non-football analysis, 7 = newspaper

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
            return Post::whereIn('category_id', $categoryIds)->where('status',1)->latest()->take($count)->get();
        });
        // $posts = [];

        $data['posts']          = [];
        $data['videos']         = [];
        $data['specialVideos']  = [];
        $data['specialPosts']   = [];
        $data['latest']         = [];
        $data['challenged']     = $this->getChallenged();
        $data['popular']        = $this->getPopular();
        foreach($posts ?? [] as $post){
            count($data['latest'])  >= $this->latestCount || in_array($post->category_id,$this->ignoreCategories) ?: $data['latest'][] =  $post;
            if($post->type === 1){
                if($post->special === 1 && count($data['latest']) < $this->spVideoCount) { $data['specialVideos'][] = $post; }
                $$data['videos'][] = $post;
            }else{
                if($post->special === 1 && count($data['latest']) < $this->spPostCount) { $data['specialPosts'][] = $post; }
                $data['posts'][$post->category_id][] = $post;
            }
        }

        return $data;

    }

    /**
     * Get the popular posts.
     * @return array
     */
    private function getPopular() : object
    {
        return Post::where('status',1)->whereNotIN('category_id',$this->ignoreCategories)->orderBy('view','DESC')->take($this->latestCount)->get();
    }

    /**
     * Get the challenged posts.
     * @return array
     */
    private function getChallenged() : object
    {
        $inventories = Post::selectRaw('posts.*,COUNT(*) as comments')->Join('comments', function($q) {
            $q->on('posts.id', '=', 'comments.commentable_id');
        });
        return $inventories->where([
            ['comments.commentable_type', Post::class],
            ['posts.status', 1],
        ])
        ->groupBy('comments.commentable_id')
        ->orderBy('comments', 'DESC')
        ->limit($this->latestCount)
        ->get();
    }
}
