<?php

namespace App\Repositories\Contracts;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

 /**
 * Interface ICommentRepository.
 */
interface ICommentRepository  {

    /**
     * Get the post comment
     * @param Post $post
     * @return Collection
     */
    public function getPostComments(Post $post) :Collection;

    /**
     * Get the status comments.
     * @return array
     */
    public function getStatusComments() :array;

}
