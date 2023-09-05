<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Models\Live;
use App\Models\Post;
use App\Repositories\Contracts\ICommentRepository;
use Illuminate\Database\Eloquent\Collection;

class CommentRepository implements ICommentRepository {

    /**
     * Get the post comment
     * @param Post $post
     * @return Collection
     */
    public function getPostComments(Post $post) :Collection {

        // return Comment::with('parents.user')->find($post->id);;

        return Comment::query()
            ->where('parent_id', 0)
            ->where('commentable_id', $post->id)
            ->where('status', 1)
            ->where('commentable_type', "App\\Models\\Post")
            ->with('parents.user')
            ->with('likes')
            ->with('likes.user')
            ->get();

    }

    /**
     * Get the status comments.
     * @return array
     */
    public function getStatusComments() :array {
        return [];
    }
}
