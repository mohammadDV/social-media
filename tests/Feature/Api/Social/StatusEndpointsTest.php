<?php

namespace Tests\Feature\Api\Social;

use App\Models\Favorite;
use App\Models\Status;

class StatusEndpointsTest extends SocialTestCase
{
    public function test_index_returns_paginated_statuses(): void
    {
        $user = $this->actingAsUser();

        Status::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => 1,
            'is_report' => 0,
            'file' => null,
        ]);

        $this->getJson('/api/statuses')
            ->assertOk()
            ->assertJsonStructure(['data', 'current_page', 'total'])
            ->assertJsonPath('total', 2);
    }

    public function test_index_for_specific_user_filters_statuses(): void
    {
        $auth = $this->actingAsUser();
        $other = $this->makeUser(['nickname' => 'other-user']);

        Status::factory()->create([
            'user_id' => $other->id,
            'status' => 1,
            'is_report' => 0,
            'file' => null,
            'text' => 'visible status',
        ]);
        Status::factory()->create([
            'user_id' => $auth->id,
            'status' => 1,
            'is_report' => 0,
            'file' => null,
            'text' => 'auth status',
        ]);

        $response = $this->getJson("/api/statuses/{$other->id}");

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertSame($other->id, $response->json('data.0.user_id'));
    }

    public function test_get_all_per_user_returns_statuses(): void
    {
        $auth = $this->actingAsUser();
        $owner = $this->makeUser();

        Status::factory()->count(3)->create([
            'user_id' => $owner->id,
            'status' => 1,
            'file' => null,
        ]);

        $this->jsonAs($auth, 'GET', "/api/status/all/{$owner->id}")
            ->assertOk()
            ->assertJsonPath('total', 3);
    }

    public function test_get_favorite_statuses_for_user(): void
    {
        $auth = $this->actingAsUser();
        $owner = $this->makeUser();

        $status = Status::factory()->create([
            'user_id' => $owner->id,
            'status' => 1,
            'file' => null,
        ]);

        Favorite::create([
            'user_id' => $owner->id,
            'favoritable_id' => $status->id,
            'favoritable_type' => Status::class,
        ]);

        $this->jsonAs($auth, 'GET', "/api/status/favorite/{$owner->id}")
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.id', $status->id);
    }

    public function test_add_favorite_creates_and_toggles_favorite(): void
    {
        $user = $this->actingAsUser();
        $status = Status::factory()->create([
            'user_id' => $this->makeUser()->id,
            'status' => 1,
            'file' => null,
        ]);

        $this->postJson("/api/status/favorite/{$status->id}")
            ->assertOk()
            ->assertJson([
                'status' => 1,
                'active' => 1,
            ]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'favoritable_id' => $status->id,
            'favoritable_type' => Status::class,
        ]);

        $this->postJson("/api/status/favorite/{$status->id}")
            ->assertOk()
            ->assertJson([
                'status' => 1,
                'active' => 0,
            ]);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'favoritable_id' => $status->id,
            'favoritable_type' => Status::class,
        ]);
    }

    public function test_preview_returns_status_info(): void
    {
        $this->actingAsUser();
        $status = Status::factory()->create([
            'user_id' => $this->makeUser()->id,
            'status' => 1,
            'is_report' => 0,
            'file' => null,
            'text' => 'preview me',
        ]);

        $this->getJson("/api/status/preview/{$status->id}")
            ->assertOk()
            ->assertJsonPath('id', $status->id)
            ->assertJsonPath('text', 'preview me');
    }
}
