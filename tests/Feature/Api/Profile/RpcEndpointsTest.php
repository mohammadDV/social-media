<?php

namespace Tests\Feature\Api\Profile;

use App\Models\Notification;
use App\Models\User;

class RpcEndpointsTest extends ProfileTestCase
{
    public function test_rpc_requires_auth(): void
    {
        $this->getJson('/api/profile/rpc')->assertUnauthorized();
    }

    public function test_rpc_returns_unread_notifications_for_user(): void
    {
        $user = $this->actingAsUser();
        $actor = User::factory()->create(['status' => 1, 'role_id' => 4]);

        Notification::create([
            'message' => 'Unread one',
            'user_id' => $user->id,
            'status' => 0,
            'type' => Notification::STATUS_SIMPLE,
            'model_id' => $actor->id,
            'model_type' => User::class,
            'link' => '/feed',
        ]);

        Notification::create([
            'message' => 'Already read',
            'user_id' => $user->id,
            'status' => 1,
            'type' => Notification::STATUS_SIMPLE,
            'model_id' => $actor->id,
            'model_type' => User::class,
        ]);

        Notification::create([
            'message' => 'Someone else',
            'user_id' => $actor->id,
            'status' => 0,
            'type' => Notification::STATUS_SIMPLE,
            'model_id' => $user->id,
            'model_type' => User::class,
        ]);

        $this->getJson('/api/profile/rpc')
            ->assertOk()
            ->assertJsonCount(1, 'notifications')
            ->assertJsonPath('notifications.0.message', 'Unread one');
    }

    public function test_rpc_works_for_admin(): void
    {
        $this->actingAsAdmin();

        $this->getJson('/api/profile/rpc')
            ->assertOk()
            ->assertJsonStructure(['notifications']);
    }
}
