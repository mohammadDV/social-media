<?php

namespace Tests\Feature\Api\Social;

use App\Models\Notification;
use App\Models\User;

class NotificationEndpointsTest extends SocialTestCase
{
    public function test_notifications_index_returns_paginated_list_and_marks_simple_as_read(): void
    {
        $auth = $this->actingAsUser();
        $actor = $this->makeUser();

        $unread = Notification::factory()->create([
            'message' => 'You have a new follower',
            'user_id' => $auth->id,
            'status' => 0,
            'type' => Notification::STATUS_SIMPLE,
            'model_id' => $actor->id,
            'model_type' => User::class,
            'link' => '/profile/followers',
        ]);

        Notification::factory()->create([
            'message' => 'Someone else notification',
            'user_id' => $actor->id,
            'status' => 0,
            'type' => Notification::STATUS_SIMPLE,
            'model_id' => $auth->id,
            'model_type' => User::class,
        ]);

        $response = $this->getJson('/api/notifications');

        $response->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.id', $unread->id)
            ->assertJsonPath('data.0.message', 'You have a new follower');

        $this->assertDatabaseHas('notifications', [
            'id' => $unread->id,
            'status' => 1,
        ]);
    }
}
