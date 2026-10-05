<?php

namespace Tests\Feature\Api\Profile;

use App\Models\Category;
use App\Models\Post;
use App\Models\Status;
use App\Models\User;
use App\Models\Video;
use App\Services\TelegramNotificationService;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

abstract class ProfileTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(TelegramNotificationService::class, function ($mock) {
            $mock->shouldReceive('sendNotification')->andReturnNull();
            $mock->shouldReceive('sendPhoto')->andReturnNull();
        });
    }

    /**
     * Authenticated user with no Spatie roles/permissions (for 403 cases).
     */
    protected function actingAsUserWithoutPermissions(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'status' => 1,
            'role_id' => 4,
            'level' => 0,
            'nickname' => 'noperm_'.uniqid(),
            'password' => bcrypt('password'),
        ], $attributes));

        Sanctum::actingAs($user, ['*']);

        return $user;
    }

    protected function createCategory(?User $user = null, array $attributes = []): Category
    {
        $user ??= User::factory()->create(['status' => 1, 'password' => bcrypt('password')]);

        return Category::factory()->create(array_merge([
            'user_id' => $user->id,
            'status' => 1,
        ], $attributes));
    }

    protected function createPost(?User $user = null, array $attributes = []): Post
    {
        $user ??= User::factory()->create(['status' => 1, 'password' => bcrypt('password')]);

        return Post::factory()->create(array_merge([
            'user_id' => $user->id,
            'status' => 1,
            'type' => 0,
            'special' => 0,
            'image' => null,
            'video' => null,
        ], $attributes));
    }

    protected function createStatus(?User $user = null, array $attributes = []): Status
    {
        $user ??= User::factory()->create(['status' => 1, 'password' => bcrypt('password')]);

        return Status::factory()->create(array_merge([
            'user_id' => $user->id,
            'status' => 1,
            'file' => null,
            'is_report' => 0,
        ], $attributes));
    }

    protected function createVideo(?User $user = null, array $attributes = []): Video
    {
        $user ??= User::factory()->create(['status' => 1, 'password' => bcrypt('password')]);

        return Video::factory()->create(array_merge([
            'user_id' => $user->id,
            'status' => 1,
            'title' => 'Sample video title',
            'file' => 'https://example.com/video.mp4',
        ], $attributes));
    }

    protected function postPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Valid post title here',
            'pre_title' => 'Pre title',
            'categories' => [1],
            'summary' => 'A short summary for the post content.',
            'content' => 'Full post content with enough length.',
            'tags' => ['football', 'news'],
            'image' => 'https://example.com/posts/image.jpg',
            'type' => 0,
            'status' => 1,
            'special' => 0,
        ], $overrides);
    }

    protected function statusPayload(array $overrides = []): array
    {
        return array_merge([
            'text' => 'This is a valid status text.',
            'status' => 1,
        ], $overrides);
    }

    protected function videoPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Valid video title here',
            'file' => 'https://example.com/videos/clip.mp4',
            'status' => 1,
        ], $overrides);
    }

    protected function userStorePayload(array $overrides = []): array
    {
        $suffix = uniqid();

        return array_merge([
            'first_name' => 'Ali',
            'last_name' => 'Testi',
            'email' => "user_{$suffix}@example.com",
            'password' => 'Password1!',
            'nickname' => "user_{$suffix}",
            'status' => 1,
            'role_id' => 4,
        ], $overrides);
    }
}
