<?php

namespace Tests\Feature\Api\Social;

use App\Models\Post;

class PostEndpointsTest extends SocialTestCase
{
    public function test_get_all_posts_per_user(): void
    {
        $auth = $this->actingAsUser();
        $owner = $this->makeUser();

        Post::factory()->count(2)->create([
            'user_id' => $owner->id,
            'status' => 1,
        ]);
        Post::factory()->create([
            'user_id' => $auth->id,
            'status' => 1,
        ]);

        $response = $this->jsonAs($auth, 'GET', "/api/post/all/{$owner->id}");

        $response->assertOk()
            ->assertJsonPath('total', 2);

        foreach ($response->json('data') as $row) {
            $this->assertSame($owner->id, $row['user_id']);
        }
    }
}
