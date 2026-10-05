<?php

namespace Tests\Feature\Api\Profile;

use App\Models\TicketSubject;

class TicketSubjectEndpointsTest extends ProfileTestCase
{
    public function test_ticket_subject_index_requires_auth(): void
    {
        $this->getJson('/api/profile/ticket-subjects')->assertUnauthorized();
    }

    public function test_ticket_subject_index_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile/ticket-subjects')->assertForbidden();
    }

    public function test_ticket_subject_index_returns_paginated_list(): void
    {
        $admin = $this->actingAsAdmin();

        TicketSubject::create([
            'title' => 'Support',
            'user_id' => $admin->id,
            'status' => 1,
        ]);

        $this->getJson('/api/profile/ticket-subjects')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.title', 'Support');
    }

    public function test_ticket_subject_show_requires_auth(): void
    {
        $subject = TicketSubject::create([
            'title' => 'Show Subject',
            'user_id' => 1,
            'status' => 1,
        ]);

        $this->getJson('/api/profile/ticket-subjects/'.$subject->id)->assertUnauthorized();
    }

    public function test_ticket_subject_show_forbidden_without_permission(): void
    {
        $admin = $this->actingAsAdmin();
        $subject = TicketSubject::create([
            'title' => 'Show Subject',
            'user_id' => $admin->id,
            'status' => 1,
        ]);

        $this->actingAsUser();

        $this->getJson('/api/profile/ticket-subjects/'.$subject->id)->assertForbidden();
    }

    public function test_ticket_subject_show_returns_subject(): void
    {
        $admin = $this->actingAsAdmin();
        $subject = TicketSubject::create([
            'title' => 'Technical',
            'user_id' => $admin->id,
            'status' => 1,
        ]);

        $this->getJson('/api/profile/ticket-subjects/'.$subject->id)
            ->assertOk()
            ->assertJsonPath('id', $subject->id)
            ->assertJsonPath('title', 'Technical');
    }

    public function test_ticket_subject_store_requires_auth(): void
    {
        $this->postJson('/api/profile/ticket-subjects', [])->assertUnauthorized();
    }

    public function test_ticket_subject_store_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/profile/ticket-subjects', [
            'title' => 'New Subject',
            'status' => 1,
        ])->assertForbidden();
    }

    public function test_ticket_subject_store_validation_fails(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/profile/ticket-subjects', [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_ticket_subject_store_creates_record(): void
    {
        $admin = $this->actingAsAdmin();

        $this->postJson('/api/profile/ticket-subjects', [
            'title' => 'Created Subject',
            'status' => 1,
        ])
            ->assertCreated()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('ticket_subjects', [
            'title' => 'Created Subject',
            'status' => 1,
            'user_id' => $admin->id,
        ]);
    }

    public function test_ticket_subject_update_requires_auth(): void
    {
        $subject = TicketSubject::create([
            'title' => 'Old Subject',
            'user_id' => 1,
            'status' => 0,
        ]);

        $this->postJson('/api/profile/ticket-subjects/'.$subject->id, [])->assertUnauthorized();
    }

    public function test_ticket_subject_update_forbidden_without_permission(): void
    {
        $admin = $this->actingAsAdmin();
        $subject = TicketSubject::create([
            'title' => 'Old Subject',
            'user_id' => $admin->id,
            'status' => 0,
        ]);

        $this->actingAsUser();

        $this->postJson('/api/profile/ticket-subjects/'.$subject->id, [
            'title' => 'Updated Subject',
            'status' => 1,
        ])->assertForbidden();
    }

    public function test_ticket_subject_update_validation_fails(): void
    {
        $admin = $this->actingAsAdmin();
        $subject = TicketSubject::create([
            'title' => 'Old Subject',
            'user_id' => $admin->id,
            'status' => 0,
        ]);

        $this->postJson('/api/profile/ticket-subjects/'.$subject->id, [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_ticket_subject_update_updates_record(): void
    {
        $admin = $this->actingAsAdmin();
        $subject = TicketSubject::create([
            'title' => 'Old Subject',
            'user_id' => $admin->id,
            'status' => 0,
        ]);

        $this->postJson('/api/profile/ticket-subjects/'.$subject->id, [
            'title' => 'Updated Subject',
            'status' => 1,
        ])
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('ticket_subjects', [
            'id' => $subject->id,
            'title' => 'Updated Subject',
            'status' => 1,
        ]);
    }

    public function test_ticket_subject_destroy_requires_auth(): void
    {
        $subject = TicketSubject::create([
            'title' => 'Delete Subject',
            'user_id' => 1,
            'status' => 1,
        ]);

        $this->deleteJson('/api/profile/ticket-subjects/'.$subject->id)->assertUnauthorized();
    }

    public function test_ticket_subject_destroy_forbidden_without_permission(): void
    {
        $admin = $this->actingAsAdmin();
        $subject = TicketSubject::create([
            'title' => 'Delete Subject',
            'user_id' => $admin->id,
            'status' => 1,
        ]);

        $this->actingAsUser();

        $this->deleteJson('/api/profile/ticket-subjects/'.$subject->id)->assertForbidden();
    }

    public function test_ticket_subject_destroy_deletes_record(): void
    {
        $admin = $this->actingAsAdmin();
        $subject = TicketSubject::create([
            'title' => 'Delete Subject',
            'user_id' => $admin->id,
            'status' => 1,
        ]);

        $this->deleteJson('/api/profile/ticket-subjects/'.$subject->id)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseMissing('ticket_subjects', ['id' => $subject->id]);
    }
}
