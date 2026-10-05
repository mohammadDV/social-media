<?php

namespace Tests\Feature\Api\Profile;

use App\Models\NotificationSend;
use App\Models\User;

class NotificationEndpointsTest extends ProfileTestCase
{
    public function test_send_list_requires_auth(): void
    {
        $this->getJson('/api/profile/notifications/send-list')->assertUnauthorized();
    }

    public function test_send_list_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile/notifications/send-list')->assertForbidden();
    }

    public function test_send_list_returns_paginated_list(): void
    {
        $admin = $this->actingAsAdmin();

        // NotificationSend uses typo `$guarder` (not `$guarded`), so assign attributes explicitly.
        $send = new NotificationSend;
        $send->user_id = $admin->id;
        $send->conditions = ['users' => '', 'roles' => ''];
        $send->users_count = 2;
        $send->send_count = 2;
        $send->status = 1;
        $send->save();

        $this->getJson('/api/profile/notifications/send-list')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.users_count', 2);
    }

    public function test_send_as_admin_requires_auth(): void
    {
        $this->postJson('/api/profile/notifications/send-as-admin', [])->assertUnauthorized();
    }

    public function test_send_as_admin_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/profile/notifications/send-as-admin', [
            'message' => 'Hello everyone',
            'has_email' => 0,
            'has_modal' => 1,
        ])->assertForbidden();
    }

    public function test_send_as_admin_validation_fails(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/profile/notifications/send-as-admin', [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_send_as_admin_creates_notifications(): void
    {
        $admin = $this->actingAsAdmin();
        $target = User::factory()->create([
            'status' => 1,
            'role_id' => 4,
            'level' => 0,
        ]);
        $target->assignRole('user');

        $this->postJson('/api/profile/notifications/send-as-admin', [
            'users' => [$target->id],
            'message' => 'Admin broadcast message',
            'link' => '/news',
            'has_email' => 0,
            'has_modal' => 1,
        ])
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('notification_sends', [
            'user_id' => $admin->id,
            'status' => 1,
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $target->id,
            'message' => 'Admin broadcast message',
            'link' => '/news',
            'is_admin' => 1,
            'has_modal' => 1,
            'model_id' => $admin->id,
            'model_type' => User::class,
        ]);
    }

    public function test_check_notification_count_requires_auth(): void
    {
        $this->postJson('/api/profile/notifications/check-notification-count', [])->assertUnauthorized();
    }

    public function test_check_notification_count_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/profile/notifications/check-notification-count', [
            'message' => 'Count me',
            'has_email' => 0,
            'has_modal' => 0,
        ])->assertForbidden();
    }

    public function test_check_notification_count_validation_fails(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/profile/notifications/check-notification-count', [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_check_notification_count_returns_count(): void
    {
        $this->actingAsAdmin();
        $target = User::factory()->create([
            'status' => 1,
            'role_id' => 4,
            'level' => 0,
        ]);

        $this->postJson('/api/profile/notifications/check-notification-count', [
            'users' => [$target->id],
            'message' => 'Count recipients',
            'has_email' => 0,
            'has_modal' => 0,
        ])
            ->assertOk()
            ->assertJsonPath('count', 1);
    }
}
