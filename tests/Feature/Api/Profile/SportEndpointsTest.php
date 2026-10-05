<?php

namespace Tests\Feature\Api\Profile;

class SportEndpointsTest extends ProfileCompetitionTestCase
{
    public function test_index_requires_authentication(): void
    {
        $this->getJson('/api/profile/sports')->assertUnauthorized();
    }

    public function test_index_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile/sports')->assertForbidden();
    }

    public function test_index_returns_paginated_sports_for_admin(): void
    {
        $admin = $this->actingAsAdmin();
        $sport = $this->createSport($admin, ['title' => 'Basketball']);

        $this->getJson('/api/profile/sports')
            ->assertOk()
            ->assertJsonFragment(['id' => $sport->id, 'title' => 'Basketball']);
    }

    public function test_show_requires_authentication(): void
    {
        $sport = $this->createSport();

        $this->getJson('/api/profile/sports/'.$sport->id)->assertUnauthorized();
    }

    public function test_show_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $sport = $this->createSport();

        $this->getJson('/api/profile/sports/'.$sport->id)->assertForbidden();
    }

    public function test_show_returns_sport_for_admin(): void
    {
        $admin = $this->actingAsAdmin();
        $sport = $this->createSport($admin, ['title' => 'Volleyball']);

        $this->getJson('/api/profile/sports/'.$sport->id)
            ->assertOk()
            ->assertJsonPath('id', $sport->id)
            ->assertJsonPath('title', 'Volleyball');
    }

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/api/profile/sports', [])->assertUnauthorized();
    }

    public function test_store_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/profile/sports', [
            'title' => 'Handball',
            'image' => 'https://example.com/h.jpg',
            'status' => 1,
        ])->assertForbidden();
    }

    public function test_store_validation_fails(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/profile/sports', [
            'title' => 'ab',
            'status' => 5,
        ])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_store_creates_sport(): void
    {
        $admin = $this->actingAsAdmin();

        $this->postJson('/api/profile/sports', [
            'title' => 'Handball',
            'alias_title' => 'handball',
            'image' => 'https://example.com/handball.jpg',
            'status' => 1,
        ])
            ->assertCreated()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('sports', [
            'title' => 'Handball',
            'alias_title' => 'handball',
            'status' => 1,
            'user_id' => $admin->id,
        ]);
    }

    public function test_update_requires_authentication(): void
    {
        $sport = $this->createSport();

        $this->postJson('/api/profile/sports/'.$sport->id, [])->assertUnauthorized();
    }

    public function test_update_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $sport = $this->createSport();

        $this->postJson('/api/profile/sports/'.$sport->id, [
            'title' => 'Updated Sport',
            'status' => 1,
        ])->assertForbidden();
    }

    public function test_update_validation_fails(): void
    {
        $admin = $this->actingAsAdmin();
        $sport = $this->createSport($admin);

        $this->postJson('/api/profile/sports/'.$sport->id, [
            'title' => 'ab',
            'status' => 9,
        ])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_update_modifies_sport(): void
    {
        $admin = $this->actingAsAdmin();
        $sport = $this->createSport($admin, ['title' => 'Old Title']);

        $this->postJson('/api/profile/sports/'.$sport->id, [
            'title' => 'Updated Sport',
            'alias_title' => 'updated-sport',
            'image' => 'https://example.com/updated.jpg',
            'status' => 0,
        ])
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('sports', [
            'id' => $sport->id,
            'title' => 'Updated Sport',
            'status' => 0,
        ]);
    }

    public function test_destroy_requires_authentication(): void
    {
        $sport = $this->createSport();

        $this->deleteJson('/api/profile/sports/'.$sport->id)->assertUnauthorized();
    }

    public function test_destroy_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $sport = $this->createSport();

        $this->deleteJson('/api/profile/sports/'.$sport->id)->assertForbidden();
    }

    public function test_destroy_deletes_sport(): void
    {
        $admin = $this->actingAsAdmin();
        $sport = $this->createSport($admin);

        $this->deleteJson('/api/profile/sports/'.$sport->id)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseMissing('sports', ['id' => $sport->id]);
    }
}
