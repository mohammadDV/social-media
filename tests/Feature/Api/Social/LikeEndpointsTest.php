<?php

namespace Tests\Feature\Api\Social;

use App\Models\Like;
use App\Models\Status;

class LikeEndpointsTest extends SocialTestCase
{
    public function test_get_likes_returns_collection(): void
    {
        $auth = $this->actingAsUser();
        $owner = $this->makeUser();
        $status = Status::factory()->create([
            'user_id' => $owner->id,
            'status' => 1,
            'file' => null,
        ]);

        Like::factory()->create([
            'user_id' => $auth->id,
            'likeable_id' => $status->id,
            'likeable_type' => Status::class,
            'type' => 1,
        ]);

        $this->postJson('/api/like/all', [
            'id' => $status->id,
            'type' => 'status',
        ])
            ->assertOk()
            ->assertJsonCount(1);
    }

    public function test_get_like_count(): void
    {
        $auth = $this->actingAsUser();
        $status = Status::factory()->create([
            'user_id' => $this->makeUser()->id,
            'status' => 1,
            'file' => null,
        ]);

        Like::factory()->count(3)->create([
            'user_id' => $this->makeUser()->id,
            'likeable_id' => $status->id,
            'likeable_type' => Status::class,
            'type' => 1,
        ]);

        $this->jsonAs($auth, 'POST', '/api/like/count', [
            'id' => $status->id,
            'type' => 'status',
        ])
            ->assertOk()
            ->assertJson(['count' => 3]);
    }

    public function test_store_like_creates_and_toggles(): void
    {
        $auth = $this->actingAsUser(['nickname' => 'liker']);
        $owner = $this->makeUser(['nickname' => 'liked-owner']);
        $status = Status::factory()->create([
            'user_id' => $owner->id,
            'status' => 1,
            'file' => null,
        ]);

        $this->postJson('/api/like', [
            'id' => $status->id,
            'type' => 'status',
        ])
            ->assertCreated()
            ->assertJson([
                'status' => 1,
                'active' => 1,
                'count' => 1,
            ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $auth->id,
            'likeable_id' => $status->id,
            'likeable_type' => Status::class,
            'type' => 1,
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'model_id' => $auth->id,
        ]);

        $this->postJson('/api/like', [
            'id' => $status->id,
            'type' => 'status',
        ])
            ->assertCreated()
            ->assertJson([
                'active' => 0,
                'count' => 0,
            ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $auth->id,
            'likeable_id' => $status->id,
            'likeable_type' => Status::class,
        ]);
    }

    public function test_like_store_validates_type(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/like', [
            'id' => 1,
            'type' => 'invalid',
        ])
            ->assertStatus(400)
            ->assertJson(['status' => 0]);
    }
}
