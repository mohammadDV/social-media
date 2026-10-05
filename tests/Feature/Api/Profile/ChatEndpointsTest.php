<?php

namespace Tests\Feature\Api\Profile;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;

class ChatEndpointsTest extends ProfileTestCase
{
    private function createChat(User $user, User $target, array $overrides = []): Chat
    {
        return Chat::create(array_merge([
            'user_id' => $user->id,
            'target_id' => $target->id,
            'status' => Chat::STATUS_ACTIVE,
        ], $overrides));
    }

    private function createMessage(Chat $chat, User $sender, array $overrides = []): ChatMessage
    {
        return ChatMessage::create(array_merge([
            'chat_id' => $chat->id,
            'user_id' => $sender->id,
            'message' => 'Hello from chat',
            'status' => ChatMessage::STATUS_PENDING,
        ], $overrides));
    }

    // ---- indexPaginate POST /api/profile/chats/ ----

    public function test_chat_index_requires_auth(): void
    {
        $this->postJson('/api/profile/chats')->assertUnauthorized();
    }

    public function test_chat_index_forbidden_without_permission(): void
    {
        $this->actingAsUserWithoutPermissions();

        $this->postJson('/api/profile/chats')->assertForbidden();
    }

    public function test_chat_index_returns_paginated_list(): void
    {
        $user = $this->actingAsUser();
        $target = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $chat = $this->createChat($user, $target);
        $this->createMessage($chat, $user);

        $this->postJson('/api/profile/chats')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.id', $chat->id);
    }

    // ---- show GET /api/profile/chats/{chat} ----

    public function test_chat_show_requires_auth(): void
    {
        $owner = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $target = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $chat = $this->createChat($owner, $target);

        $this->getJson('/api/profile/chats/'.$chat->id)->assertUnauthorized();
    }

    public function test_chat_show_forbidden_without_permission(): void
    {
        $owner = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $target = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $chat = $this->createChat($owner, $target);

        $this->actingAsUserWithoutPermissions();

        $this->getJson('/api/profile/chats/'.$chat->id)->assertForbidden();
    }

    public function test_chat_show_returns_messages_and_marks_pending_as_read(): void
    {
        $user = $this->actingAsUser();
        $target = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $chat = $this->createChat($user, $target);
        $incoming = $this->createMessage($chat, $target, [
            'message' => 'Incoming pending message',
            'status' => ChatMessage::STATUS_PENDING,
        ]);
        $this->createMessage($chat, $user, [
            'message' => 'My own message',
            'status' => ChatMessage::STATUS_PENDING,
        ]);

        $this->getJson('/api/profile/chats/'.$chat->id)
            ->assertOk()
            ->assertJsonPath('total', 2)
            ->assertJsonPath('data.0.message', 'My own message');

        $this->assertDatabaseHas('chat_messages', [
            'id' => $incoming->id,
            'status' => ChatMessage::STATUS_READ,
        ]);
    }

    // ---- chatInfo GET /api/profile/chats/info/{chat} ----

    public function test_chat_info_requires_auth(): void
    {
        $owner = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $target = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $chat = $this->createChat($owner, $target);

        $this->getJson('/api/profile/chats/info/'.$chat->id)->assertUnauthorized();
    }

    public function test_chat_info_forbidden_without_permission(): void
    {
        $owner = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $target = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $chat = $this->createChat($owner, $target);

        $this->actingAsUserWithoutPermissions();

        $this->getJson('/api/profile/chats/info/'.$chat->id)->assertForbidden();
    }

    public function test_chat_info_returns_chat_with_participants(): void
    {
        $user = $this->actingAsUser();
        $target = User::factory()->create([
            'status' => 1,
            'role_id' => 4,
            'nickname' => 'chat_target_'.uniqid(),
        ]);
        $chat = $this->createChat($user, $target);

        $this->getJson('/api/profile/chats/info/'.$chat->id)
            ->assertOk()
            ->assertJsonPath('id', $chat->id)
            ->assertJsonPath('user_id', $user->id)
            ->assertJsonPath('target_id', $target->id)
            ->assertJsonPath('block', false)
            ->assertJsonPath('banned', false);
    }

    // ---- store POST /api/profile/chats/{user} ----

    public function test_chat_store_requires_auth(): void
    {
        $target = User::factory()->create(['status' => 1, 'role_id' => 4]);

        $this->postJson('/api/profile/chats/'.$target->id, [
            'message' => 'Hello',
        ])->assertUnauthorized();
    }

    public function test_chat_store_forbidden_without_permission(): void
    {
        $target = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $this->actingAsUserWithoutPermissions();

        $this->postJson('/api/profile/chats/'.$target->id, [
            'message' => 'Hello',
        ])->assertForbidden();
    }

    public function test_chat_store_validation_fails_without_message(): void
    {
        $user = $this->actingAsUser();
        $target = User::factory()->create(['status' => 1, 'role_id' => 4]);

        $this->postJson('/api/profile/chats/'.$target->id, [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_chat_store_creates_chat_and_message(): void
    {
        $user = $this->actingAsUser();
        $target = User::factory()->create(['status' => 1, 'role_id' => 4]);

        $this->postJson('/api/profile/chats/'.$target->id, [
            'message' => 'First private message',
        ])
            ->assertCreated()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('chats', [
            'user_id' => $user->id,
            'target_id' => $target->id,
        ]);

        $chat = Chat::query()
            ->where('user_id', $user->id)
            ->where('target_id', $target->id)
            ->first();

        $this->assertNotNull($chat);
        $this->assertDatabaseHas('chat_messages', [
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'message' => 'First private message',
        ]);
    }

    public function test_chat_store_reuses_existing_chat(): void
    {
        $user = $this->actingAsUser();
        $target = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $chat = $this->createChat($user, $target);

        $this->postJson('/api/profile/chats/'.$target->id, [
            'message' => 'Follow-up message',
        ])
            ->assertCreated()
            ->assertJsonPath('status', 1);

        $this->assertEquals(1, Chat::query()->where('user_id', $user->id)->where('target_id', $target->id)->count());
        $this->assertDatabaseHas('chat_messages', [
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'message' => 'Follow-up message',
        ]);
    }

    // ---- deleteMessages POST /api/profile/chats/delete/{chat} ----

    public function test_chat_delete_messages_requires_auth(): void
    {
        $owner = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $target = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $chat = $this->createChat($owner, $target);

        $this->postJson('/api/profile/chats/delete/'.$chat->id)->assertUnauthorized();
    }

    public function test_chat_delete_messages_forbidden_without_permission(): void
    {
        $owner = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $target = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $chat = $this->createChat($owner, $target);

        $this->actingAsUserWithoutPermissions();

        $this->postJson('/api/profile/chats/delete/'.$chat->id)->assertForbidden();
    }

    public function test_chat_delete_messages_sets_remover_id(): void
    {
        $user = $this->actingAsUser();
        $target = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $chat = $this->createChat($user, $target);
        $message = $this->createMessage($chat, $user, [
            'message' => 'Message to soft-delete',
        ]);

        $this->postJson('/api/profile/chats/delete/'.$chat->id)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('chat_messages', [
            'id' => $message->id,
            'remover_id' => $user->id,
        ]);
    }

    public function test_chat_delete_messages_hard_deletes_when_other_party_already_removed(): void
    {
        $user = $this->actingAsUser();
        $target = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $chat = $this->createChat($user, $target);
        $message = $this->createMessage($chat, $target, [
            'message' => 'Already removed by target',
            'remover_id' => $target->id,
        ]);

        $this->postJson('/api/profile/chats/delete/'.$chat->id)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseMissing('chat_messages', [
            'id' => $message->id,
        ]);
    }
}
