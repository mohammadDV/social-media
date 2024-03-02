<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Repositories\Contracts\IPostRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller
{
    /**
     * Constructor of PostController.
     */
    public function __construct(protected IPostRepository $repository)
    {
        //
    }

    /**
     * Get all of post except newspaper.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->repository->index(range(1,7), 200), Response::HTTP_OK);
    }

    /**
     * Get the post info.
     */
    public function getPostInfo(Post $post): JsonResponse
    {
        return response()->json($this->repository->getPostInfo($post), Response::HTTP_OK);
    }

    /**
     * Get all of post per category.
     */
    public function getPostsPerCategory(Category $category): JsonResponse
    {
        return response()->json($this->repository->getPostsPerCategory($category), Response::HTTP_OK);
    }

    /**
     * Get all of post per category.
     */
    public function search(Request $request): JsonResponse
    {
        return response()->json($this->repository->search($request->input('search', '')), Response::HTTP_OK);
    }

    /**
     * Get all of post per category.
     */
    public function searchPostTag(Request $request): JsonResponse
    {
        return response()->json($this->repository->searchPostTag($request->input('search')), Response::HTTP_OK);
    }
}
