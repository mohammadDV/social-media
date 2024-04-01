<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Post;
use App\Models\Status;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

 /**
 * Interface ICommentRepository.
 */
interface ICommentRepository  {

    /**
     * Get the post comment
     * @param Post $post
     */
    public function getPostComments(Post $post);

    /**
     * Get the post comment
     * @param StoreCommentRequest $request
     * @param Post $post
     * @return JsonResponse
     */
    public function storePostComment(StoreCommentRequest $request, Post $post) :JsonResponse;

    /**
     * Get the status comments.
     * @param Status $status
     */
    public function getStatusComments(Status $status);

    /**
     * Get the status comment
     * @param StoreCommentRequest $request
     * @param Status $status
     * @return JsonResponse
     */
    public function storeStatusComment(StoreCommentRequest $request, Status $status) :JsonResponse;

}
