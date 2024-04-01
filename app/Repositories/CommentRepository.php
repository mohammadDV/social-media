<?php

namespace App\Repositories;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Status;
use App\Models\User;
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
     */
    public function getPostComments(Post $post)
    {

        return Comment::query()
            ->where('is_report', 0)
            ->where('parent_id', 0)
            ->where('commentable_id', $post->id)
            ->where('status', 1)
            ->where('commentable_type', "App\\Models\\Post")
            ->with('user')
            ->with('parents.user')
            ->with('likes')
            ->with('likes.user')
            ->orderBy('id', 'DESC')
            ->paginate(2);

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

        $parentId = $request->input('parent_id',0);

        // Store the comment
        $comment = Auth::user()->comments()->create([
            "text"              => $request->input('comment'),
            "parent_id"         => $parentId,
            "status"            => 1,
            "commentable_id"    => $post->id,
            "commentable_type"  => Post::class,
        ]);

        if ($comment){

            if (!empty($parentId)) {
                $parentComment = Comment::find($parentId);
                $commentOwner = $parentComment->user;
                // Add a notification for comment owner
                if (!empty($commentOwner->id)) {
                    $this->addNotification(
                        $commentOwner,
                        '/news/' . $post->id . '/' . $post->slug,
                        __('site.Someone sent a replay to your comment.', ['someone' => Auth::user()->nickname])
                    );
                }

            }

            // Add a notification for status owner
            $this->addNotification(
                $post->user,
                '/news/' . $post->id . '/' . $post->slug,
                __('site.Someone sent a comment to your post.', ['someone' => Auth::user()->nickname])
            );

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
     */
    public function getStatusComments(Status $status)
    {

        return Comment::query()
            ->where('is_report', 0)
            ->where('parent_id', 0)
            ->where('commentable_id', $status->id)
            ->where('status', 1)
            ->where('commentable_type', "App\\Models\\Status")
            ->with('user')
            ->with('parents.user')
            ->with('likes')
            ->with('likes.user')
            ->orderBy('id', 'DESC')
            ->paginate(10);

    }

    /**
     * Get the status comment
     * @param StoreCommentRequest $request
     * @param Status $status
     * @return JsonResponse
     */
    public function storeStatusComment(StoreCommentRequest $request, Status $status) :JsonResponse
    {

        $parentId = $request->input('parent_id',0);

        // Store the comment
        $comment = Auth::user()->comments()->create([
            "text"              => $request->input('comment'),
            "parent_id"         => $parentId,
            "status"            => 1,
            "commentable_id"    => $status->id,
            "commentable_type"  => Status::class,
        ]);

        if ($comment){

            if (!empty($parentId)) {
                $parentComment = Comment::find($parentId);
                $commentOwner = $parentComment->user;
                // Add a notification for comment owner
                if (!empty($commentOwner->id)) {
                    $this->addNotification(
                        $commentOwner,
                        '/profile/' . $status->id,
                        __('site.Someone sent a replay to your comment.', ['someone' => Auth::user()->nickname])
                    );
                }

            }

            // Add a notification for status owner
            $this->addNotification(
                $status->user,
                '/profile/' . $status->id,
                __('site.Someone sent a comment to your status.', ['someone' => Auth::user()->nickname])
            );

            return response()->json([
                'status'    => 1,
                'message'   => __('site.Your comment has been stored successfully'),
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();

    }

    /**
     * Add a notification
     * @param $model
     * @param string $link
     * @param string $message
     * @return void
     */
    public function addNotification($model, string $link, string $message) :void
    {
        if ($model->id == Auth::user()->id) {
            return;
        }

        cache()->remember(
            'notification.status.comment' . Auth::user()->id . '.' . $model->id,
            now()->addMinutes(1),
            function () use($model, $link, $message) {
                // Add notification
                return Notification::create([
                    'message' => $message,
                    'link' => $link,
                    'user_id' => $model->id,
                    'model_id' => Auth::user()->id,
                    'model_type' => User::class,
                    'has_email' => 1,
                ]);
            });
    }
}
