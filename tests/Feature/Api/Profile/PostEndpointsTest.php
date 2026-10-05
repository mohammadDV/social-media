<?php

namespace Tests\Feature\Api\Profile;

use App\Models\Post;
use PHPUnit\Framework\Attributes\DataProvider;

class PostEndpointsTest extends ProfileTestCase
{
    #[DataProvider('unauthenticatedRoutesProvider')]
    public function test_unauthenticated_requests_return_401(string $method, string $uri): void
    {
        $this->json($method, $uri)->assertUnauthorized();
    }

    public static function unauthenticatedRoutesProvider(): array
    {
        return [
            'index' => ['GET', '/api/profile/posts'],
            'store' => ['POST', '/api/profile/posts'],
            'show' => ['GET', '/api/profile/posts/1'],
            'update' => ['POST', '/api/profile/posts/1'],
            'destroy' => ['DELETE', '/api/profile/posts/1'],
            'realDestroy' => ['DELETE', '/api/profile/posts/delete/1'],
        ];
    }

    public function test_user_without_permission_gets_403_on_collection_routes(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile/posts')->assertForbidden();
        $this->postJson('/api/profile/posts', [])->assertForbidden();
    }

    public function test_user_without_permission_gets_403_on_resource_routes(): void
    {
        $owner = $this->actingAsAdmin();
        $post = $this->createPost($owner);
        $trashed = $this->createPost($owner);
        $trashed->delete();

        $this->actingAsUser();

        $this->getJson("/api/profile/posts/{$post->id}")->assertForbidden();
        $this->postJson("/api/profile/posts/{$post->id}", [])->assertForbidden();
        $this->deleteJson("/api/profile/posts/{$post->id}")->assertForbidden();
        $this->deleteJson("/api/profile/posts/delete/{$trashed->id}")->assertForbidden();
    }

    public function test_admin_can_index_posts(): void
    {
        $admin = $this->actingAsAdmin();
        $this->createPost($admin);
        $this->createPost($admin);

        $this->getJson('/api/profile/posts')
            ->assertOk()
            ->assertJsonStructure(['data', 'total']);
    }

    public function test_admin_can_show_post(): void
    {
        $admin = $this->actingAsAdmin();
        $post = $this->createPost($admin);
        $category = $this->createCategory($admin);
        $post->categories()->attach($category->id);

        $this->getJson("/api/profile/posts/{$post->id}")
            ->assertOk()
            ->assertJsonPath('id', $post->id)
            ->assertJsonPath('title', $post->title);
    }

    public function test_store_validation_fails_with_empty_payload(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/profile/posts', [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_admin_can_store_post(): void
    {
        $admin = $this->actingAsAdmin();
        $category = $this->createCategory($admin);

        $payload = $this->postPayload([
            'categories' => [$category->id],
            'title' => 'Brand new profile post title',
        ]);

        $this->postJson('/api/profile/posts', $payload)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('posts', [
            'title' => 'Brand new profile post title',
            'user_id' => $admin->id,
            'status' => 1,
        ]);
    }

    public function test_duplicate_store_within_window_creates_only_one_post(): void
    {
        $admin = $this->actingAsAdmin();
        $category = $this->createCategory($admin);

        $payload = $this->postPayload([
            'categories' => [$category->id],
            'title' => 'Duplicate guarded post title',
            'content' => 'Same content body for both submits.',
        ]);

        $this->postJson('/api/profile/posts', $payload)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->postJson('/api/profile/posts', $payload)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertSame(
            1,
            Post::query()
                ->where('user_id', $admin->id)
                ->where('title', 'Duplicate guarded post title')
                ->count()
        );
    }

    public function test_update_validation_fails_with_empty_payload(): void
    {
        $admin = $this->actingAsAdmin();
        $post = $this->createPost($admin);

        $this->postJson("/api/profile/posts/{$post->id}", [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_admin_can_update_post(): void
    {
        $admin = $this->actingAsAdmin();
        $post = $this->createPost($admin, [
            'title' => 'Original post title here',
            'send_to_telegram' => 1,
        ]);
        $category = $this->createCategory($admin);

        $payload = $this->postPayload([
            'categories' => [$category->id],
            'title' => 'Updated profile post title',
            'image' => 'https://example.com/posts/updated.jpg',
        ]);

        $this->postJson("/api/profile/posts/{$post->id}", $payload)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated profile post title',
        ]);
    }

    public function test_admin_can_soft_delete_post(): void
    {
        $admin = $this->actingAsAdmin();
        $post = $this->createPost($admin);

        $this->deleteJson("/api/profile/posts/{$post->id}")
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    public function test_admin_can_force_delete_post(): void
    {
        $admin = $this->actingAsAdmin();
        $post = $this->createPost($admin, [
            'image' => null,
            'video' => null,
        ]);
        $post->delete();

        $this->deleteJson("/api/profile/posts/delete/{$post->id}")
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
        $this->assertNull(Post::withTrashed()->find($post->id));
    }
}
