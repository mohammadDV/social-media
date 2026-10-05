<?php

namespace Tests\Feature\Api\Social;

use App\Models\Block;
use App\Models\Follow;

class BlockEndpointsTest extends SocialTestCase
{
    public function test_block_users_lists_blocked_users(): void
    {
        $auth = $this->actingAsUser();
        $blocked = $this->makeUser([
            'first_name' => 'Blocked',
            'nickname' => 'blocked-user',
        ]);

        Block::create([
            'blocker_id' => $auth->id,
            'user_id' => $blocked->id,
        ]);

        $this->getJson('/api/block-users')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.id', $blocked->id);
    }

    public function test_store_block_creates_block_and_removes_follows(): void
    {
        $auth = $this->actingAsUser();
        $target = $this->makeUser(['nickname' => 'to-block']);

        Follow::factory()->create([
            'user_id' => $target->id,
            'follower_id' => $auth->id,
            'status' => Follow::STATUS_ACCEPTED,
        ]);

        $this->postJson("/api/block/{$target->id}")
            ->assertOk()
            ->assertJson([
                'status' => 1,
                'block' => 1,
            ]);

        $this->assertDatabaseHas('blocks', [
            'blocker_id' => $auth->id,
            'user_id' => $target->id,
        ]);

        $this->assertDatabaseMissing('follows', [
            'user_id' => $target->id,
            'follower_id' => $auth->id,
        ]);
    }

    public function test_store_block_toggles_off_existing_block(): void
    {
        $auth = $this->actingAsUser();
        $target = $this->makeUser();

        Block::create([
            'blocker_id' => $auth->id,
            'user_id' => $target->id,
        ]);

        $this->postJson("/api/block/{$target->id}")
            ->assertOk()
            ->assertJson([
                'status' => 1,
                'block' => 0,
            ]);

        $this->assertDatabaseMissing('blocks', [
            'blocker_id' => $auth->id,
            'user_id' => $target->id,
        ]);
    }
}
