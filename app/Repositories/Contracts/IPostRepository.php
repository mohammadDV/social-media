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
interface IPostRepository
{
    /**
     * Get the posts.
     */
    public function index(array $categories, int $count): array;

    /**
     * Get the suggested posts.
     */
    public function suggested(): array;

    /**
     * Get the author posts.
     */
    public function authorPosts(User $user);

    /**
     * Get the post.
     *
     * @return array
     */
    public function show(Post $post);

    /**
     * Get the post info.
     */
    public function getPostInfo(Post $post): PostResource;

    /**
     * Get the posts for the user.
     *
     * @param  ?User  $user
     */
    public function getAllPerUser(User $user): LengthAwarePaginator;

    /**
     * Get all of post per category.
     */
    public function getPostsPerCategory(Category $category): array;

    /**
     * Get searched posts.
     */
    public function search(string $search): AnonymousResourceCollection;

    /**
     * Get searched posts.
     */
    public function searchPostTag(string $search): array;

    /**
     * Get all posts.
     */
    public function postPaginate(Request $request): LengthAwarePaginator;

    /**
     * Store the post.
     */
    public function store(PostRequest $request): JsonResponse;

    /**
     * Update the post.
     *
     * @throws \Exception
     */
    public function update(PostUpdateRequest $request, Post $post): JsonResponse;

    /**
     * Delete the post.
     *
     * @throws \Exception
     */
    public function destroy(Post $post): JsonResponse;

    /**
     * Delete completely the post.
     */
    public function realDestroy(int $id): JsonResponse;
}
