<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Models\Post;
use App\Repositories\Contracts\ICommentRepository;
use App\Repositories\Contracts\IPostRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    /**
     * Constructor of PostController.
     */
    public function __construct(protected ICommentRepository $repository)
    {
        //
    }

    /**
     * Get the post comment
     * @param Post $post
     * @return JsonResponse
     */
    public function getPostComments(Post $post): JsonResponse
    {
        return response()->json($this->repository->getPostComments($post), Response::HTTP_OK);
    }

    /**
     * Store the post.
     */
    public function store(PostRequest $request): JsonResponse
    {
        return $this->repository->store($request);
    }

    /**
     * Update the post.
     * @param PostUpdateRequest $request
     * @param Post $post
     * @return JsonResponse
     */
    public function update(PostUpdateRequest $request, Post $post): JsonResponse
    {
        return $this->repository->update($request, $post);
    }

    /**
     * Delete the post.
     * @param Post $post
     * @return JsonResponse
     */
    public function destroy(Post $post): JsonResponse
    {
        return $this->repository->destroy($post);
    }

    /**
     * Delete completely the post.
     * @param int $id
     * @return JsonResponse
     */
    public function realDestroy(int $id): JsonResponse
    {
        return $this->repository->realDestroy($id);
    }
}
