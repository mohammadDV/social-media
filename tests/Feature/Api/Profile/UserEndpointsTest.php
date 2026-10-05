<?php

namespace Tests\Feature\Api\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;

class UserEndpointsTest extends ProfileTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'api.pwnedpasswords.com/*' => Http::response('', 200),
        ]);
    }

    #[DataProvider('unauthenticatedRoutesProvider')]
    public function test_unauthenticated_requests_return_401(string $method, string $uri): void
    {
        $this->json($method, $uri)->assertUnauthorized();
    }

    public static function unauthenticatedRoutesProvider(): array
    {
        return [
            'index' => ['GET', '/api/profile/users'],
            'show' => ['GET', '/api/profile/users/info'],
            'store' => ['POST', '/api/profile/users'],
            'update' => ['POST', '/api/profile/users/1'],
            'password' => ['PATCH', '/api/profile/users/password'],
            'destroy' => ['DELETE', '/api/profile/users/1'],
        ];
    }

    public function test_user_without_permission_gets_403_on_collection_routes(): void
    {
        // user_update is granted to the seeded "user" role — strip all permissions.
        $this->actingAsUserWithoutPermissions();

        $this->getJson('/api/profile/users')->assertForbidden();
        $this->getJson('/api/profile/users/info')->assertForbidden();
        $this->postJson('/api/profile/users', [])->assertForbidden();
        $this->patchJson('/api/profile/users/password', [])->assertForbidden();
    }

    public function test_user_without_permission_gets_403_on_resource_routes(): void
    {
        $target = User::factory()->create([
            'status' => 1,
            'role_id' => 4,
            'nickname' => 'forbidden_'.uniqid(),
        ]);

        $this->actingAsUserWithoutPermissions();

        $this->postJson("/api/profile/users/{$target->id}", [])->assertForbidden();
        $this->deleteJson("/api/profile/users/{$target->id}")->assertForbidden();
    }

    public function test_admin_can_paginate_users(): void
    {
        $this->actingAsAdmin();
        User::factory()->count(2)->create(['status' => 1, 'role_id' => 4]);

        $this->getJson('/api/profile/users')
            ->assertOk()
            ->assertJsonStructure(['data', 'total']);
    }

    public function test_admin_can_show_own_info_without_id(): void
    {
        $admin = $this->actingAsAdmin(['nickname' => 'admin_profile_'.uniqid()]);

        $this->getJson('/api/profile/users/info')
            ->assertOk()
            ->assertJsonPath('id', $admin->id)
            ->assertJsonPath('nickname', $admin->nickname);
    }

    public function test_admin_can_show_another_user(): void
    {
        $this->actingAsAdmin();
        $target = User::factory()->create([
            'status' => 1,
            'role_id' => 4,
            'nickname' => 'target_'.uniqid(),
        ]);

        $this->getJson("/api/profile/users/info/{$target->id}")
            ->assertOk()
            ->assertJsonPath('id', $target->id)
            ->assertJsonPath('nickname', $target->nickname);
    }

    public function test_store_validation_fails_with_empty_payload(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/profile/users', [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_admin_can_store_user(): void
    {
        $this->actingAsAdmin();
        $payload = $this->userStorePayload([
            'nickname' => 'created_user_'.uniqid(),
            'email' => 'created_'.uniqid().'@example.com',
        ]);

        $this->postJson('/api/profile/users', $payload)
            ->assertCreated()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('users', [
            'email' => $payload['email'],
            'nickname' => $payload['nickname'],
            'first_name' => 'Ali',
            'status' => 1,
            'role_id' => 4,
        ]);
    }

    public function test_update_validation_fails_with_empty_payload(): void
    {
        $admin = $this->actingAsAdmin();
        $target = User::factory()->create([
            'status' => 1,
            'role_id' => 4,
            'nickname' => 'upd_'.uniqid(),
        ]);

        $this->postJson("/api/profile/users/{$target->id}", [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_admin_can_update_user(): void
    {
        $this->actingAsAdmin();
        $target = User::factory()->create([
            'status' => 1,
            'role_id' => 4,
            'nickname' => 'before_'.uniqid(),
            'first_name' => 'Before',
        ]);

        $nickname = 'after_'.uniqid();
        $payload = [
            'first_name' => 'After',
            'last_name' => 'Name',
            'nickname' => $nickname,
            'profile_photo_path' => 'https://example.com/avatar.jpg',
            'bg_photo_path' => 'https://example.com/bg.jpg',
            'status' => 1,
            'is_private' => false,
            'role_id' => 4,
        ];

        $this->postJson("/api/profile/users/{$target->id}", $payload)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'first_name' => 'After',
            'nickname' => $nickname,
        ]);
    }

    public function test_password_update_validation_fails_with_empty_payload(): void
    {
        $this->actingAsAdmin();

        $this->patchJson('/api/profile/users/password', [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_admin_can_update_own_password(): void
    {
        $admin = $this->actingAsAdmin([
            'password' => bcrypt('password'),
        ]);

        $this->patchJson('/api/profile/users/password', [
            'current_password' => 'password',
            'password' => 'NewPass1!',
            'confirm_password' => 'NewPass1!',
        ])
            ->assertCreated()
            ->assertJsonPath('status', 1);

        $admin->refresh();
        $this->assertTrue(Hash::check('NewPass1!', $admin->password));
    }

    public function test_admin_can_report_user_via_destroy(): void
    {
        $this->actingAsAdmin();
        $target = User::factory()->create([
            'status' => 1,
            'role_id' => 4,
            'is_report' => 0,
            'nickname' => 'report_'.uniqid(),
        ]);

        $this->deleteJson("/api/profile/users/{$target->id}")
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'is_report' => 1,
        ]);
    }
}
