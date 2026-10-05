<?php

namespace Tests\Feature\Api\Profile;

use App\Models\Status;
use PHPUnit\Framework\Attributes\DataProvider;

class StatusEndpointsTest extends ProfileTestCase
{
    #[DataProvider('unauthenticatedRoutesProvider')]
    public function test_unauthenticated_requests_return_401(string $method, string $uri): void
    {
        $this->json($method, $uri)->assertUnauthorized();
    }

    public static function unauthenticatedRoutesProvider(): array
    {
        return [
            'index' => ['GET', '/api/profile/status'],
            'store' => ['POST', '/api/profile/status'],
            'update' => ['POST', '/api/profile/status/1'],
            'destroy' => ['DELETE', '/api/profile/status/1'],
            'realDestroy' => ['DELETE', '/api/profile/status/delete/1'],
        ];
    }

    public function test_user_without_permission_gets_403_on_collection_routes(): void
    {
        // Seeded "user" role includes status_* — use a user with no permissions.
        $this->actingAsUserWithoutPermissions();

        $this->getJson('/api/profile/status')->assertForbidden();
        $this->postJson('/api/profile/status', [])->assertForbidden();
    }

    public function test_user_without_permission_gets_403_on_resource_routes(): void
    {
        $owner = $this->actingAsAdmin();
        $status = $this->createStatus($owner);
        $statusId = $status->id;
        $status->delete();

        $this->actingAsUserWithoutPermissions();

        // Soft-deleted rows 404 on model-bound update/destroy; create a live row for those.
        $live = $this->createStatus($owner);

        $this->postJson("/api/profile/status/{$live->id}", [])->assertForbidden();
        $this->deleteJson("/api/profile/status/{$live->id}")->assertForbidden();
        $this->deleteJson("/api/profile/status/delete/{$statusId}")->assertForbidden();
    }

    public function test_admin_can_index_statuses(): void
    {
        $admin = $this->actingAsAdmin();
        $this->createStatus($admin);
        $this->createStatus($admin);

        $this->getJson('/api/profile/status')
            ->assertOk()
            ->assertJsonStructure(['data', 'total']);
    }

    public function test_store_validation_fails_with_empty_payload(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/profile/status', [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_admin_can_store_status(): void
    {
        $admin = $this->actingAsAdmin();

        $this->postJson('/api/profile/status', $this->statusPayload([
            'text' => 'Fresh status published by admin.',
        ]))
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('statuses', [
            'user_id' => $admin->id,
            'text' => 'Fresh status published by admin.',
            'status' => 1,
        ]);
    }

    public function test_update_validation_fails_with_empty_payload(): void
    {
        $admin = $this->actingAsAdmin();
        $status = $this->createStatus($admin);

        $this->postJson("/api/profile/status/{$status->id}", [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_admin_can_update_status(): void
    {
        $admin = $this->actingAsAdmin();
        $status = $this->createStatus($admin, [
            'text' => 'Original status text content.',
        ]);

        $this->postJson("/api/profile/status/{$status->id}", $this->statusPayload([
            'text' => 'Updated status text content.',
            'status' => 0,
        ]))
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('statuses', [
            'id' => $status->id,
            'text' => 'Updated status text content.',
            'status' => 0,
        ]);
    }

    public function test_admin_can_soft_delete_status(): void
    {
        $admin = $this->actingAsAdmin();
        $status = $this->createStatus($admin);

        $this->deleteJson("/api/profile/status/{$status->id}")
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertSoftDeleted('statuses', ['id' => $status->id]);
    }

    public function test_admin_can_force_delete_status(): void
    {
        $admin = $this->actingAsAdmin();
        $status = $this->createStatus($admin);
        $status->delete();

        $this->deleteJson("/api/profile/status/delete/{$status->id}")
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseMissing('statuses', ['id' => $status->id]);
        $this->assertNull(Status::withTrashed()->find($status->id));
    }
}
