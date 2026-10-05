<?php

namespace Tests\Feature\Api\Profile;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\TicketSubject;
use App\Models\User;

class TicketEndpointsTest extends ProfileTestCase
{
    private function createSubject(User $user, array $overrides = []): TicketSubject
    {
        return TicketSubject::create(array_merge([
            'title' => 'Billing',
            'user_id' => $user->id,
            'status' => 1,
        ], $overrides));
    }

    public function test_ticket_index_requires_auth(): void
    {
        $this->getJson('/api/profile/tickets')->assertUnauthorized();
    }

    public function test_ticket_index_forbidden_without_permission(): void
    {
        $this->actingAsUserWithoutPermissions();

        $this->getJson('/api/profile/tickets')->assertForbidden();
    }

    public function test_ticket_index_returns_paginated_list(): void
    {
        $admin = $this->actingAsAdmin();
        $subject = $this->createSubject($admin);
        $owner = User::factory()->create(['status' => 1, 'role_id' => 4]);

        Ticket::create([
            'user_id' => $owner->id,
            'subject_id' => $subject->id,
            'status' => Ticket::STATUS_ACTIVE,
        ]);

        $this->getJson('/api/profile/tickets')
            ->assertOk()
            ->assertJsonPath('total', 1);
    }

    public function test_ticket_show_requires_auth(): void
    {
        $owner = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $subject = $this->createSubject($owner);
        $ticket = Ticket::create([
            'user_id' => $owner->id,
            'subject_id' => $subject->id,
            'status' => Ticket::STATUS_ACTIVE,
        ]);

        $this->getJson('/api/profile/tickets/'.$ticket->id)->assertUnauthorized();
    }

    public function test_ticket_show_forbidden_without_permission(): void
    {
        $admin = $this->actingAsAdmin();
        $subject = $this->createSubject($admin);
        $ticket = Ticket::create([
            'user_id' => $admin->id,
            'subject_id' => $subject->id,
            'status' => Ticket::STATUS_ACTIVE,
        ]);

        $this->actingAsUserWithoutPermissions();

        $this->getJson('/api/profile/tickets/'.$ticket->id)->assertForbidden();
    }

    public function test_ticket_show_returns_ticket_with_messages(): void
    {
        $admin = $this->actingAsAdmin();
        $subject = $this->createSubject($admin);
        $ticket = Ticket::create([
            'user_id' => $admin->id,
            'subject_id' => $subject->id,
            'status' => Ticket::STATUS_ACTIVE,
        ]);
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'message' => 'Initial message',
        ]);

        $this->getJson('/api/profile/tickets/'.$ticket->id)
            ->assertOk()
            ->assertJsonPath('id', $ticket->id)
            ->assertJsonPath('messages.0.message', 'Initial message');
    }

    public function test_ticket_store_requires_auth(): void
    {
        $this->postJson('/api/profile/tickets', [])->assertUnauthorized();
    }

    public function test_ticket_store_forbidden_without_permission(): void
    {
        $this->actingAsUserWithoutPermissions();

        $this->postJson('/api/profile/tickets', [
            'subject_id' => 1,
            'message' => 'Help please',
        ])->assertForbidden();
    }

    public function test_ticket_store_validation_fails(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/profile/tickets', [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_ticket_store_creates_ticket_and_message(): void
    {
        $admin = $this->actingAsAdmin();
        $subject = $this->createSubject($admin);

        $user = $this->actingAsUser();

        $this->postJson('/api/profile/tickets', [
            'subject_id' => $subject->id,
            'message' => 'I need help with billing',
        ])
            ->assertCreated()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('tickets', [
            'user_id' => $user->id,
            'subject_id' => $subject->id,
            'status' => Ticket::STATUS_ACTIVE,
        ]);

        $ticket = Ticket::query()->where('user_id', $user->id)->first();

        $this->assertDatabaseHas('ticket_messages', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => 'I need help with billing',
        ]);
    }

    public function test_ticket_store_message_requires_auth(): void
    {
        $owner = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $subject = $this->createSubject($owner);
        $ticket = Ticket::create([
            'user_id' => $owner->id,
            'subject_id' => $subject->id,
            'status' => Ticket::STATUS_ACTIVE,
        ]);

        $this->postJson('/api/profile/tickets/'.$ticket->id, [
            'message' => 'Follow up',
        ])->assertUnauthorized();
    }

    public function test_ticket_store_message_forbidden_without_permission(): void
    {
        $admin = $this->actingAsAdmin();
        $subject = $this->createSubject($admin);
        $ticket = Ticket::create([
            'user_id' => $admin->id,
            'subject_id' => $subject->id,
            'status' => Ticket::STATUS_ACTIVE,
        ]);

        $this->actingAsUserWithoutPermissions();

        $this->postJson('/api/profile/tickets/'.$ticket->id, [
            'message' => 'Follow up message',
        ])->assertForbidden();
    }

    public function test_ticket_store_message_validation_fails(): void
    {
        $admin = $this->actingAsAdmin();
        $subject = $this->createSubject($admin);
        $ticket = Ticket::create([
            'user_id' => $admin->id,
            'subject_id' => $subject->id,
            'status' => Ticket::STATUS_ACTIVE,
        ]);

        $this->postJson('/api/profile/tickets/'.$ticket->id, [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_ticket_store_message_as_admin_adds_reply(): void
    {
        $admin = $this->actingAsAdmin();
        $subject = $this->createSubject($admin);
        $owner = User::factory()->create(['status' => 1, 'role_id' => 4, 'level' => 0]);
        $ticket = Ticket::create([
            'user_id' => $owner->id,
            'subject_id' => $subject->id,
            'status' => Ticket::STATUS_ACTIVE,
        ]);
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $owner->id,
            'message' => 'User question',
        ]);

        $this->postJson('/api/profile/tickets/'.$ticket->id, [
            'message' => 'Admin reply here',
        ])
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('ticket_messages', [
            'ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'message' => 'Admin reply here',
        ]);
    }

    public function test_ticket_change_status_requires_auth(): void
    {
        $owner = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $subject = $this->createSubject($owner);
        $ticket = Ticket::create([
            'user_id' => $owner->id,
            'subject_id' => $subject->id,
            'status' => Ticket::STATUS_ACTIVE,
        ]);

        $this->postJson('/api/profile/tickets/status/'.$ticket->id, [
            'status' => 'closed',
        ])->assertUnauthorized();
    }

    public function test_ticket_change_status_forbidden_without_permission(): void
    {
        $admin = $this->actingAsAdmin();
        $subject = $this->createSubject($admin);
        $ticket = Ticket::create([
            'user_id' => $admin->id,
            'subject_id' => $subject->id,
            'status' => Ticket::STATUS_ACTIVE,
        ]);

        $this->actingAsUserWithoutPermissions();

        $this->postJson('/api/profile/tickets/status/'.$ticket->id, [
            'status' => 'closed',
        ])->assertForbidden();
    }

    public function test_ticket_change_status_validation_fails(): void
    {
        $admin = $this->actingAsAdmin();
        $subject = $this->createSubject($admin);
        $ticket = Ticket::create([
            'user_id' => $admin->id,
            'subject_id' => $subject->id,
            'status' => Ticket::STATUS_ACTIVE,
        ]);

        $this->postJson('/api/profile/tickets/status/'.$ticket->id, [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_ticket_change_status_updates_status(): void
    {
        $admin = $this->actingAsAdmin();
        $subject = $this->createSubject($admin);
        $ticket = Ticket::create([
            'user_id' => $admin->id,
            'subject_id' => $subject->id,
            'status' => Ticket::STATUS_ACTIVE,
        ]);

        $this->postJson('/api/profile/tickets/status/'.$ticket->id, [
            'status' => Ticket::STATUS_CLOSED,
        ])
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => Ticket::STATUS_CLOSED,
        ]);
    }
}
