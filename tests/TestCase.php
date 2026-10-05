<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
    }

    protected function actingAsUser(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'status' => 1,
            'role_id' => 4,
            'password' => bcrypt('password'),
        ], $attributes));

        $user->assignRole('user');
        Sanctum::actingAs($user, ['*']);

        return $user;
    }

    protected function actingAsAdmin(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'status' => 1,
            'role_id' => 1,
            'level' => 3,
            'password' => bcrypt('password'),
        ], $attributes));

        $user->syncRoles(['admin']);
        Sanctum::actingAs($user, ['*']);

        return $user;
    }

    protected function actingAsAuthor(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'status' => 1,
            'role_id' => 2,
            'password' => bcrypt('password'),
        ], $attributes));

        $user->syncRoles(['author']);
        Sanctum::actingAs($user, ['*']);

        return $user;
    }

    protected function jsonAs(User $user, string $method, string $uri, array $data = [], array $headers = [])
    {
        Sanctum::actingAs($user, ['*']);

        return $this->json($method, $uri, $data, $headers);
    }
}
