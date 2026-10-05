<?php

namespace Tests\Feature\Api\Profile;

use PHPUnit\Framework\Attributes\DataProvider;

class RoleEndpointsTest extends ProfileTestCase
{
    #[DataProvider('unauthenticatedRoutesProvider')]
    public function test_unauthenticated_requests_return_401(string $method, string $uri): void
    {
        $this->json($method, $uri)->assertUnauthorized();
    }

    public static function unauthenticatedRoutesProvider(): array
    {
        return [
            'roles' => ['GET', '/api/profile/role'],
            'permissions' => ['GET', '/api/profile/role/permissions'],
        ];
    }

    #[DataProvider('forbiddenRoutesProvider')]
    public function test_user_without_permission_gets_403(string $method, string $uri): void
    {
        $this->actingAsUser();

        $this->json($method, $uri)->assertForbidden();
    }

    public static function forbiddenRoutesProvider(): array
    {
        return [
            'roles' => ['GET', '/api/profile/role'],
            'permissions' => ['GET', '/api/profile/role/permissions'],
        ];
    }

    public function test_admin_can_list_roles(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/profile/role');

        $response->assertOk();

        $names = collect($response->json())->pluck('name')->all();
        $this->assertContains('admin', $names);
        $this->assertContains('user', $names);
        $this->assertContains('author', $names);
    }

    public function test_admin_can_list_permissions(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/profile/role/permissions');

        $response->assertOk();

        $names = collect($response->json())->pluck('name')->all();
        $this->assertContains('post_show', $names);
        $this->assertContains('role_show', $names);
        $this->assertContains('permission_show', $names);
        $this->assertNotEmpty($names);
    }
}
