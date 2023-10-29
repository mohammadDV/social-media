<?php

namespace App\Repositories;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Status;
use App\Repositories\Contracts\ICommentRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CommentRepository implements ICommentRepository {

    /**
     * Get the post comment
     * @param Post $post
     * @return CommentResource
     */
    public function getPostComments(Post $post) :AnonymousResourceCollection
    {

        $comments = Comment::query()
            ->where('parent_id', 0)
            ->where('commentable_id', $post->id)
            ->where('status', 1)
            ->where('commentable_type', "App\\Models\\Post")
            ->with('parents.user')
            ->with('likes')
            ->with('likes.user')
            ->get();

        return CommentResource::collection($comments);


    }

    /**
     * Get the post comment
     * @param StoreCommentRequest $request
     * @param Post $post
     * @return JsonResponse
     * @throws \Exception
     */
    public function storePostComment(StoreCommentRequest $request, Post $post) :JsonResponse
    {

        // Store the comment
        $comment = Auth::user()->comments()->create([
            "text"              => $request->input('comment'),
            "parent_id"         => $request->input('parent_id',0),
            "status"            => 1,
            "commentable_id"    => $post->id,
            "commentable_type"  => Post::class,
        ]);

        if ($comment){
            return response()->json([
                'status'    => 1,
                'message'   => [__('site.Your comment has been stored successfully')],
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();

    }

    /**
     * Get the status comments.
     * @param Status $status
     * @return Collection
     */
    public function getStatusComments(Status $status) :Collection
    {
        return Comment::query()
            ->where('parent_id', 0)
            ->where('commentable_id', $status->id)
            ->where('status', 1)
            ->where('commentable_type', "App\\Models\\Status")
            ->with('user')
            ->with('parents.user')
            ->with('likes')
            ->with('likes.user')
            ->get();
    }

    /**
     * Get the status comment
     * @param StoreCommentRequest $request
     * @param Status $status
     * @return JsonResponse
     */
    public function storeStatusComment(StoreCommentRequest $request, Status $status) :JsonResponse
    {

        // Store the comment
        $comment = Auth::user()->comments()->create([
            "text"              => $request->input('comment'),
            "parent_id"         => $request->input('parent_id',0),
            "status"            => 1,
            "commentable_id"    => $status->id,
            "commentable_type"  => Status::class,
        ]);

        if ($comment){
            return response()->json([
                'status'    => 1,
                'message'   => __('site.Your comment has been stored successfully'),
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();

    }
}
