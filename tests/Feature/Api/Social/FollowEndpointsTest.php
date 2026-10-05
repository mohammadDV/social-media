<?php

namespace Tests\Feature\Api\Social;

use App\Models\Follow;
use App\Models\User;

class FollowEndpointsTest extends SocialTestCase
{
    public function test_follow_info_returns_counts_and_lists(): void
    {
        $auth = $this->actingAsUser(['nickname' => 'me']);
        $follower = $this->makeUser(['nickname' => 'follower-one']);
        $following = $this->makeUser(['nickname' => 'following-one']);

        Follow::factory()->create([
            'user_id' => $auth->id,
            'follower_id' => $follower->id,
            'status' => Follow::STATUS_ACCEPTED,
        ]);
        Follow::factory()->create([
            'user_id' => $following->id,
            'follower_id' => $auth->id,
            'status' => Follow::STATUS_ACCEPTED,
        ]);

        $this->getJson('/api/follow-info')
            ->assertOk()
            ->assertJsonPath('followersCount', 1)
            ->assertJsonPath('followingsCount', 1)
            ->assertJsonPath('info.id', $auth->id);
    }

    public function test_follow_info_for_another_user(): void
    {
        $auth = $this->actingAsUser();
        $target = $this->makeUser(['nickname' => 'target']);

        Follow::factory()->create([
            'user_id' => $target->id,
            'follower_id' => $this->makeUser()->id,
            'status' => Follow::STATUS_ACCEPTED,
        ]);

        $this->jsonAs($auth, 'GET', "/api/follow-info/{$target->id}")
            ->assertOk()
            ->assertJsonPath('info.id', $target->id)
            ->assertJsonPath('followersCount', 1);
    }

    public function test_get_followers(): void
    {
        $auth = $this->actingAsUser();
        $follower = $this->makeUser(['first_name' => 'Ali', 'nickname' => 'ali']);

        Follow::factory()->create([
            'user_id' => $auth->id,
            'follower_id' => $follower->id,
            'status' => Follow::STATUS_ACCEPTED,
        ]);

        $this->getJson('/api/followers')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.follower_id', $follower->id);
    }

    public function test_get_followings(): void
    {
        $auth = $this->actingAsUser();
        $following = $this->makeUser(['first_name' => 'Sara', 'nickname' => 'sara']);

        Follow::factory()->create([
            'user_id' => $following->id,
            'follower_id' => $auth->id,
            'status' => Follow::STATUS_ACCEPTED,
        ]);

        $this->getJson('/api/followings')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.user_id', $following->id);
    }

    public function test_store_follow_creates_follow_and_notification(): void
    {
        $auth = $this->actingAsUser(['nickname' => 'requester']);
        $target = $this->makeUser(['nickname' => 'target-user']);

        $this->postJson("/api/follow/{$target->id}")
            ->assertOk()
            ->assertJson([
                'status' => 1,
                'active' => 1,
                'follow' => 1,
            ]);

        $this->assertDatabaseHas('follows', [
            'user_id' => $target->id,
            'follower_id' => $auth->id,
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $target->id,
            'model_id' => $auth->id,
            'model_type' => User::class,
        ]);
    }

    public function test_store_follow_toggles_off_existing_follow(): void
    {
        $auth = $this->actingAsUser();
        $target = $this->makeUser();

        Follow::factory()->create([
            'user_id' => $target->id,
            'follower_id' => $auth->id,
            'status' => Follow::STATUS_PENDING,
        ]);

        $this->postJson("/api/follow/{$target->id}")
            ->assertOk()
            ->assertJson([
                'active' => 0,
                'follow' => 0,
            ]);

        $this->assertDatabaseMissing('follows', [
            'user_id' => $target->id,
            'follower_id' => $auth->id,
        ]);
    }

    public function test_is_follower_returns_flags(): void
    {
        $auth = $this->actingAsUser();
        $target = $this->makeUser();

        Follow::factory()->create([
            'user_id' => $target->id,
            'follower_id' => $auth->id,
            'status' => Follow::STATUS_ACCEPTED,
        ]);

        $this->getJson("/api/is-follower/{$target->id}")
            ->assertOk()
            ->assertJson([
                'active' => true,
                'block' => false,
                'notfound' => false,
            ]);
    }

    public function test_change_follow_status_accepts_pending_request(): void
    {
        $auth = $this->actingAsUser(['nickname' => 'owner']);
        $follower = $this->makeUser(['nickname' => 'pending-follower']);

        Follow::factory()->create([
            'user_id' => $auth->id,
            'follower_id' => $follower->id,
            'status' => Follow::STATUS_PENDING,
        ]);

        $this->postJson("/api/follow-chaneg-status/{$follower->id}", [
            'status' => Follow::STATUS_ACCEPTED,
        ])
            ->assertOk()
            ->assertJson([
                'status' => 1,
                'active' => 1,
            ]);

        $this->assertDatabaseHas('follows', [
            'user_id' => $auth->id,
            'follower_id' => $follower->id,
            'status' => Follow::STATUS_ACCEPTED,
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $follower->id,
            'model_id' => $auth->id,
        ]);
    }

    public function test_change_follow_status_rejects_and_deletes(): void
    {
        $auth = $this->actingAsUser();
        $follower = $this->makeUser();

        Follow::factory()->create([
            'user_id' => $auth->id,
            'follower_id' => $follower->id,
            'status' => Follow::STATUS_PENDING,
        ]);

        $this->postJson("/api/follow-chaneg-status/{$follower->id}", [
            'status' => Follow::STATUS_REJECTED,
        ])
            ->assertOk()
            ->assertJson([
                'active' => 0,
            ]);

        $this->assertDatabaseMissing('follows', [
            'user_id' => $auth->id,
            'follower_id' => $follower->id,
        ]);
    }
}
