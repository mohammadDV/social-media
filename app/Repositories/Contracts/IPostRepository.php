<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\PostRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface IPostRepository.
 */
interface IPostRepository  {

    /**
     * Get the posts.
     * @param $categories
     * @param $count
     * @return array
     */
    public function index(array $categories, int $count) :array;

    /**
     * Get the post.
     * @param Post $post
     * @return array
     */
    public function show(Post $post);

    /**
     * Get the post info.
     * @param Post $post
     * @return PostResource
     */
    public function getPostInfo(Post $post) :PostResource;

    /**
     * Get the posts for the user.
     * @param ?User $user
     * @return LengthAwarePaginator
     */
    public function getAllPerUser(User $user) :LengthAwarePaginator;

    /**
     * Get all of post per category.
     * @param Category $category
     * @return array
     */
    public function getPostsPerCategory(Category $category) :array;

    /**
     * Get searched posts.
     * @param search $search
     * @return AnonymousResourceCollection
     */
    public function search(string $search) :AnonymousResourceCollection;

    /**
     * Get searched posts.
     * @param search $search
     * @return array
     */
    public function searchPostTag(string $search) :array;

    /**
     * Get all posts.
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function postPaginate(Request $request) :LengthAwarePaginator;

    /**
     * Store the post.
     *
     * @param  PostRequest  $request
     * @return JsonResponse
     */
    public function store(PostRequest $request) :JsonResponse;

    /**
     * Update the post.
     *
     * @param  PostUpdateRequest  $request
     * @param  Post  $post
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(PostUpdateRequest $request, Post $post) :JsonResponse;

    /**
     * Delete the post.
     *
     * @param  Post  $post
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(Post $post) :JsonResponse;

    /**
     * Delete completely the post.
     * @param int $id
     * @return JsonResponse
     */
    public function realDestroy(int $id): JsonResponse;

}
