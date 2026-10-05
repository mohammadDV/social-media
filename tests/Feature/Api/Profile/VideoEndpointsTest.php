<?php

namespace Tests\Feature\Api\Profile;

use PHPUnit\Framework\Attributes\DataProvider;

class VideoEndpointsTest extends ProfileTestCase
{
    #[DataProvider('unauthenticatedRoutesProvider')]
    public function test_unauthenticated_requests_return_401(string $method, string $uri): void
    {
        $this->json($method, $uri)->assertUnauthorized();
    }

    public static function unauthenticatedRoutesProvider(): array
    {
        return [
            'index' => ['GET', '/api/profile/videos/index'],
            'paginate' => ['GET', '/api/profile/videos'],
            'show' => ['GET', '/api/profile/videos/1'],
            'store' => ['POST', '/api/profile/videos'],
            'update' => ['POST', '/api/profile/videos/1'],
            'destroy' => ['DELETE', '/api/profile/videos/1'],
        ];
    }

    public function test_user_without_permission_gets_403_on_collection_routes(): void
    {
        $this->actingAsUser();

        // /videos/index has no permission middleware — covered in a happy-path test.
        $this->getJson('/api/profile/videos')->assertForbidden();
        $this->postJson('/api/profile/videos', [])->assertForbidden();
    }

    public function test_user_without_permission_gets_403_on_resource_routes(): void
    {
        $owner = $this->actingAsAdmin();
        $video = $this->createVideo($owner);

        $this->actingAsUser();

        $this->getJson("/api/profile/videos/{$video->id}")->assertForbidden();
        $this->postJson("/api/profile/videos/{$video->id}", [])->assertForbidden();
        $this->deleteJson("/api/profile/videos/{$video->id}")->assertForbidden();
    }

    public function test_authenticated_user_can_list_active_videos_without_permission(): void
    {
        $user = $this->actingAsUser();
        $this->createVideo($user, ['status' => 1, 'title' => 'User owned video title']);

        $this->getJson('/api/profile/videos/index')
            ->assertOk()
            ->assertJsonFragment(['title' => 'User owned video title']);
    }

    public function test_admin_can_paginate_videos(): void
    {
        $admin = $this->actingAsAdmin();
        $this->createVideo($admin);
        $this->createVideo($admin);

        $this->getJson('/api/profile/videos')
            ->assertOk()
            ->assertJsonStructure(['data', 'total']);
    }

    public function test_admin_can_show_video(): void
    {
        $admin = $this->actingAsAdmin();
        $video = $this->createVideo($admin, ['title' => 'Visible video title']);

        $this->getJson("/api/profile/videos/{$video->id}")
            ->assertOk()
            ->assertJsonPath('id', $video->id)
            ->assertJsonPath('title', 'Visible video title');
    }

    public function test_store_validation_fails_with_empty_payload(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/profile/videos', [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_admin_can_store_video(): void
    {
        $admin = $this->actingAsAdmin();

        $this->postJson('/api/profile/videos', $this->videoPayload([
            'title' => 'Fresh uploaded video title',
        ]))
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('videos', [
            'user_id' => $admin->id,
            'title' => 'Fresh uploaded video title',
            'status' => 1,
            'file' => 'https://example.com/videos/clip.mp4',
        ]);
    }

    public function test_update_validation_fails_with_empty_payload(): void
    {
        $admin = $this->actingAsAdmin();
        $video = $this->createVideo($admin);

        $this->postJson("/api/profile/videos/{$video->id}", [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_admin_can_update_video(): void
    {
        $admin = $this->actingAsAdmin();
        $video = $this->createVideo($admin, ['title' => 'Old video title here']);

        $this->postJson("/api/profile/videos/{$video->id}", $this->videoPayload([
            'title' => 'Renamed video title here',
            'file' => 'https://example.com/videos/new.mp4',
            'status' => 0,
        ]))
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('videos', [
            'id' => $video->id,
            'title' => 'Renamed video title here',
            'file' => 'https://example.com/videos/new.mp4',
            'status' => 0,
        ]);
    }

    public function test_admin_can_delete_video(): void
    {
        $admin = $this->actingAsAdmin();
        $video = $this->createVideo($admin);

        $this->deleteJson("/api/profile/videos/{$video->id}")
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseMissing('videos', ['id' => $video->id]);
    }
}
