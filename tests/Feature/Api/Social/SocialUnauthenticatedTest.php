<?php

namespace Tests\Feature\Api\Social;

use App\Models\Post;
use App\Models\Status;

class SocialUnauthenticatedTest extends SocialTestCase
{
    public function test_representative_social_endpoints_require_authentication(): void
    {
        $user = $this->makeUser();
        $status = Status::factory()->create([
            'user_id' => $user->id,
            'status' => 1,
            'file' => null,
        ]);
        $post = Post::factory()->create(['user_id' => $user->id]);

        $endpoints = [
            ['GET', '/api/statuses'],
            ['GET', "/api/status/all/{$user->id}"],
            ['GET', "/api/post/all/{$user->id}"],
            ['GET', "/api/status/favorite/{$user->id}"],
            ['POST', "/api/status/favorite/{$status->id}"],
            ['GET', "/api/status/preview/{$status->id}"],
            ['GET', '/api/new-members'],
            ['GET', '/api/congenial-members'],
            ['GET', '/api/follow-info'],
            ['GET', '/api/followers'],
            ['GET', '/api/followings'],
            ['POST', "/api/follow-chaneg-status/{$user->id}"],
            ['GET', "/api/is-follower/{$user->id}"],
            ['POST', "/api/follow/{$user->id}"],
            ['GET', '/api/block-users'],
            ['POST', "/api/block/{$user->id}"],
            ['POST', "/api/comment/post/{$post->id}"],
            ['GET', "/api/comment/status/{$status->id}"],
            ['POST', "/api/comment/status/{$status->id}"],
            ['POST', '/api/like/all'],
            ['POST', '/api/like/count'],
            ['POST', '/api/like'],
            ['GET', '/api/favorite/clubs'],
            ['GET', '/api/favorite/clubs-limited'],
            ['POST', '/api/favorite/clubs/search'],
            ['POST', '/api/user/search'],
            ['GET', '/api/user/info'],
            ['GET', '/api/sport/index'],
            ['GET', '/api/country/index'],
            ['GET', '/api/ticket-subjects/index'],
            ['GET', '/api/notifications'],
            ['POST', '/api/upload-image'],
            ['POST', '/api/upload-video'],
            ['POST', '/api/upload-file'],
        ];

        foreach ($endpoints as [$method, $uri]) {
            $this->json($method, $uri)->assertUnauthorized();
        }
    }
}
