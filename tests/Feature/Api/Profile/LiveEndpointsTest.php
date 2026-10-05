<?php

namespace Tests\Feature\Api\Profile;

class LiveEndpointsTest extends ProfileCompetitionTestCase
{
    public function test_index_list_requires_authentication(): void
    {
        $this->getJson('/api/profile/lives/index')->assertUnauthorized();
    }

    public function test_index_list_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile/lives/index')->assertForbidden();
    }

    public function test_index_list_returns_lives_for_admin(): void
    {
        $admin = $this->actingAsAdmin();
        $this->createLive($admin, [
            'title' => 'Night Live Event',
            'date' => '2026-10-05 22:00',
        ]);

        $this->getJson('/api/profile/lives/index')
            ->assertOk();
    }

    public function test_index_paginate_requires_authentication(): void
    {
        $this->getJson('/api/profile/lives')->assertUnauthorized();
    }

    public function test_index_paginate_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile/lives')->assertForbidden();
    }

    public function test_index_paginate_returns_lives_for_admin(): void
    {
        $admin = $this->actingAsAdmin();
        $live = $this->createLive($admin, ['title' => 'Paged Live Show']);

        $this->getJson('/api/profile/lives')
            ->assertOk()
            ->assertJsonFragment(['id' => $live->id, 'title' => 'Paged Live Show']);
    }

    public function test_show_requires_authentication(): void
    {
        $live = $this->createLive();

        $this->getJson('/api/profile/lives/'.$live->id)->assertUnauthorized();
    }

    public function test_show_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $live = $this->createLive();

        $this->getJson('/api/profile/lives/'.$live->id)->assertForbidden();
    }

    public function test_show_returns_live_for_admin(): void
    {
        $admin = $this->actingAsAdmin();
        $live = $this->createLive($admin, ['title' => 'Show Live']);

        $this->getJson('/api/profile/lives/'.$live->id)
            ->assertOk()
            ->assertJsonPath('id', $live->id)
            ->assertJsonPath('title', 'Show Live');
    }

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/api/profile/lives', [])->assertUnauthorized();
    }

    public function test_store_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/profile/lives', [
            'title' => 'New Live Event',
            'teams' => 'Alpha - Beta',
            'date' => '2026-10-05 18:00',
            'status' => 1,
            'priority' => 1,
        ])->assertForbidden();
    }

    public function test_store_validation_fails(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/profile/lives', [
            'title' => 'ab',
            'teams' => 'x',
            'date' => 'y',
            'status' => 5,
            'priority' => 500,
        ])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_store_creates_live(): void
    {
        $admin = $this->actingAsAdmin();

        $this->postJson('/api/profile/lives', [
            'title' => 'Created Live Event',
            'teams' => 'Red - Blue',
            'date' => '2026-10-09 19:30',
            'link' => 'https://example.com/created-live',
            'info' => 'Prime time game',
            'status' => 1,
            'priority' => 3,
        ])
            ->assertCreated()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('lives', [
            'title' => 'Created Live Event',
            'teams' => 'Red - Blue',
            'user_id' => $admin->id,
            'status' => 1,
            'priority' => 3,
        ]);
    }

    public function test_update_requires_authentication(): void
    {
        $live = $this->createLive();

        $this->postJson('/api/profile/lives/'.$live->id, [])->assertUnauthorized();
    }

    public function test_update_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $live = $this->createLive();

        $this->postJson('/api/profile/lives/'.$live->id, [
            'title' => 'Updated Live Event',
            'teams' => 'Gamma - Delta',
            'date' => '2026-10-10 20:00',
            'status' => 1,
            'priority' => 2,
        ])->assertForbidden();
    }

    public function test_update_validation_fails(): void
    {
        $admin = $this->actingAsAdmin();
        $live = $this->createLive($admin);

        $this->postJson('/api/profile/lives/'.$live->id, [
            'title' => 'ab',
            'teams' => 'x',
            'date' => 'y',
            'status' => 9,
            'priority' => 200,
        ])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_update_modifies_live(): void
    {
        $admin = $this->actingAsAdmin();
        $live = $this->createLive($admin, ['title' => 'Old Live']);

        $this->postJson('/api/profile/lives/'.$live->id, [
            'title' => 'Updated Live Event',
            'teams' => 'Gamma - Delta',
            'date' => '2026-10-10 20:00',
            'link' => 'https://example.com/updated-live',
            'info' => 'Updated info text',
            'status' => 0,
            'priority' => 5,
        ])
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('lives', [
            'id' => $live->id,
            'title' => 'Updated Live Event',
            'teams' => 'Gamma - Delta',
            'status' => 0,
            'priority' => 5,
        ]);
    }

    public function test_destroy_requires_authentication(): void
    {
        $live = $this->createLive();

        $this->deleteJson('/api/profile/lives/'.$live->id)->assertUnauthorized();
    }

    public function test_destroy_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $live = $this->createLive();

        $this->deleteJson('/api/profile/lives/'.$live->id)->assertForbidden();
    }

    public function test_destroy_deletes_live(): void
    {
        $admin = $this->actingAsAdmin();
        $live = $this->createLive($admin);

        $this->deleteJson('/api/profile/lives/'.$live->id)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseMissing('lives', ['id' => $live->id]);
    }
}
