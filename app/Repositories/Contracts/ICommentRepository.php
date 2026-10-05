<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Post;
use App\Models\Status;
use Illuminate\Http\JsonResponse;

/**
 * Interface ICommentRepository.
 */
interface ICommentRepository
{
    /**
     * Get the post comment
     */
    public function getPostComments(Post $post);

    /**
     * Get the post comment
     */
    public function storePostComment(StoreCommentRequest $request, Post $post): JsonResponse;

    /**
     * Get the status comments.
     */
    public function getStatusComments(Status $status);

    /**
     * Get the status comment
     */
    public function storeStatusComment(StoreCommentRequest $request, Status $status): JsonResponse;
}
