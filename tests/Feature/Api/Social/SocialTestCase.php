<?php

namespace Tests\Feature\Api\Social;

use App\Models\User;
use App\Services\TelegramNotificationService;
use Tests\TestCase;

abstract class SocialTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(TelegramNotificationService::class, function ($mock) {
            $mock->shouldReceive('sendNotification')->andReturnNull();
            $mock->shouldReceive('sendPhoto')->andReturnNull();
        });
    }

    protected function makeUser(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'status' => 1,
            'role_id' => 4,
            'level' => 0,
            'password' => bcrypt('password'),
        ], $attributes));

        $user->assignRole('user');

        return $user;
    }
}
