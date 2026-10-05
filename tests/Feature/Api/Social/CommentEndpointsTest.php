<?php

namespace Tests\Feature\Api\Social;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Status;

class CommentEndpointsTest extends SocialTestCase
{
    public function test_store_post_comment_persists_comment(): void
    {
        $auth = $this->actingAsUser(['nickname' => 'commenter']);
        $author = $this->makeUser(['nickname' => 'post-author']);
        $post = Post::factory()->create(['user_id' => $author->id]);

        $this->postJson("/api/comment/post/{$post->id}", [
            'comment' => 'Great article here',
        ])
            ->assertCreated()
            ->assertJson([
                'status' => 1,
            ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $auth->id,
            'commentable_id' => $post->id,
            'commentable_type' => Post::class,
            'text' => 'Great article here',
            'status' => 1,
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $author->id,
            'model_id' => $auth->id,
        ]);
    }

    public function test_store_status_comment_persists_comment(): void
    {
        $auth = $this->actingAsUser(['nickname' => 'status-commenter']);
        $owner = $this->makeUser(['nickname' => 'status-owner']);
        $status = Status::factory()->create([
            'user_id' => $owner->id,
            'status' => 1,
            'file' => null,
        ]);

        $this->postJson("/api/comment/status/{$status->id}", [
            'comment' => 'Nice status update',
        ])
            ->assertCreated()
            ->assertJson([
                'status' => 1,
            ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $auth->id,
            'commentable_id' => $status->id,
            'commentable_type' => Status::class,
            'text' => 'Nice status update',
        ]);
    }

    public function test_get_status_comments_returns_paginated_list(): void
    {
        $auth = $this->actingAsUser();
        $owner = $this->makeUser();
        $status = Status::factory()->create([
            'user_id' => $owner->id,
            'status' => 1,
            'file' => null,
        ]);

        Comment::factory()->count(2)->create([
            'user_id' => $auth->id,
            'commentable_id' => $status->id,
            'commentable_type' => Status::class,
            'parent_id' => 0,
            'status' => 1,
            'is_report' => 0,
            'image' => null,
        ]);

        $this->getJson("/api/comment/status/{$status->id}")
            ->assertOk()
            ->assertJsonPath('total', 2);
    }

    public function test_store_comment_validates_min_length(): void
    {
        $this->actingAsUser();
        $post = Post::factory()->create(['user_id' => $this->makeUser()->id]);

        $this->postJson("/api/comment/post/{$post->id}", [
            'comment' => 'hi',
        ])
            ->assertStatus(400)
            ->assertJson(['status' => 0]);
    }
}
