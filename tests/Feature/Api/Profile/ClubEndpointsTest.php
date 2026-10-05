<?php

namespace Tests\Feature\Api\Profile;

use App\Models\FavoriteClub;

class ClubEndpointsTest extends ProfileCompetitionTestCase
{
    public function test_all_requires_authentication(): void
    {
        $this->getJson('/api/profile/clubs/all')->assertUnauthorized();
    }

    public function test_all_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile/clubs/all')->assertForbidden();
    }

    public function test_all_returns_clubs_for_admin(): void
    {
        $admin = $this->actingAsAdmin();
        $sport = $this->createSport($admin);
        $country = $this->createCountry($admin);
        $club = $this->createClub($admin, $sport, $country, ['title' => 'All Club']);

        $this->getJson('/api/profile/clubs/all/'.$sport->id.'/'.$country->id)
            ->assertOk()
            ->assertJsonFragment(['id' => $club->id, 'title' => 'All Club']);
    }

    public function test_index_requires_authentication(): void
    {
        $this->getJson('/api/profile/clubs')->assertUnauthorized();
    }

    public function test_index_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile/clubs')->assertForbidden();
    }

    public function test_index_returns_paginated_clubs_for_admin(): void
    {
        $admin = $this->actingAsAdmin();
        $club = $this->createClub($admin, null, null, ['title' => 'Paged Club']);

        $this->getJson('/api/profile/clubs')
            ->assertOk()
            ->assertJsonFragment(['id' => $club->id, 'title' => 'Paged Club']);
    }

    public function test_is_active_requires_authentication(): void
    {
        $club = $this->createClub();

        $this->getJson('/api/profile/clubs/active/'.$club->id)->assertUnauthorized();
    }

    public function test_is_active_returns_false_when_not_favorited(): void
    {
        $admin = $this->actingAsAdmin();
        $club = $this->createClub($admin);

        $this->getJson('/api/profile/clubs/active/'.$club->id)
            ->assertOk()
            ->assertJson(['active' => false]);
    }

    public function test_is_active_returns_true_when_favorited(): void
    {
        $admin = $this->actingAsAdmin();
        $club = $this->createClub($admin);

        FavoriteClub::query()->create([
            'user_id' => $admin->id,
            'club_id' => $club->id,
        ]);

        $this->getJson('/api/profile/clubs/active/'.$club->id)
            ->assertOk()
            ->assertJson(['active' => true]);
    }

    public function test_is_active_toggle_via_favorite_state(): void
    {
        $admin = $this->actingAsAdmin();
        $club = $this->createClub($admin);

        $this->getJson('/api/profile/clubs/active/'.$club->id)
            ->assertOk()
            ->assertJson(['active' => false]);

        FavoriteClub::query()->create([
            'user_id' => $admin->id,
            'club_id' => $club->id,
        ]);

        $this->getJson('/api/profile/clubs/active/'.$club->id)
            ->assertOk()
            ->assertJson(['active' => true]);

        FavoriteClub::query()
            ->where('user_id', $admin->id)
            ->where('club_id', $club->id)
            ->delete();

        $this->getJson('/api/profile/clubs/active/'.$club->id)
            ->assertOk()
            ->assertJson(['active' => false]);
    }

    public function test_show_requires_authentication(): void
    {
        $club = $this->createClub();

        $this->getJson('/api/profile/clubs/'.$club->id)->assertUnauthorized();
    }

    public function test_show_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $club = $this->createClub();

        $this->getJson('/api/profile/clubs/'.$club->id)->assertForbidden();
    }

    public function test_show_returns_club_for_admin(): void
    {
        $admin = $this->actingAsAdmin();
        $club = $this->createClub($admin, null, null, ['title' => 'Show Club']);

        $this->getJson('/api/profile/clubs/'.$club->id)
            ->assertOk()
            ->assertJsonPath('id', $club->id)
            ->assertJsonPath('title', 'Show Club');
    }

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/api/profile/clubs', [])->assertUnauthorized();
    }

    public function test_store_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $sport = $this->createSport();
        $country = $this->createCountry();

        $this->postJson('/api/profile/clubs', [
            'title' => 'New Club',
            'sport_id' => $sport->id,
            'country_id' => $country->id,
            'image' => 'https://example.com/c.jpg',
            'status' => 1,
        ])->assertForbidden();
    }

    public function test_store_validation_fails(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/profile/clubs', [
            'title' => '',
            'status' => 5,
        ])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_store_creates_club(): void
    {
        $admin = $this->actingAsAdmin();
        $sport = $this->createSport($admin);
        $country = $this->createCountry($admin);

        $this->postJson('/api/profile/clubs', [
            'title' => 'Created Club',
            'alias_title' => 'created-club',
            'sport_id' => $sport->id,
            'country_id' => $country->id,
            'image' => 'https://example.com/created-club.jpg',
            'status' => 1,
        ])
            ->assertCreated()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('clubs', [
            'title' => 'Created Club',
            'sport_id' => $sport->id,
            'country_id' => $country->id,
            'user_id' => $admin->id,
            'status' => 1,
        ]);
    }

    public function test_update_requires_authentication(): void
    {
        $club = $this->createClub();

        $this->postJson('/api/profile/clubs/'.$club->id, [])->assertUnauthorized();
    }

    public function test_update_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $club = $this->createClub();

        $this->postJson('/api/profile/clubs/'.$club->id, [
            'title' => 'Updated Club',
            'sport_id' => $club->sport_id,
            'country_id' => $club->country_id,
            'status' => 1,
        ])->assertForbidden();
    }

    public function test_update_validation_fails(): void
    {
        $admin = $this->actingAsAdmin();
        $club = $this->createClub($admin);

        $this->postJson('/api/profile/clubs/'.$club->id, [
            'title' => 'Updated Club',
            'status' => 9,
        ])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_update_modifies_club(): void
    {
        $admin = $this->actingAsAdmin();
        $club = $this->createClub($admin, null, null, ['title' => 'Old Club']);

        $this->postJson('/api/profile/clubs/'.$club->id, [
            'title' => 'Updated Club',
            'alias_title' => 'updated-club',
            'sport_id' => $club->sport_id,
            'country_id' => $club->country_id,
            'image' => 'https://example.com/updated-club.jpg',
            'status' => 0,
        ])
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('clubs', [
            'id' => $club->id,
            'title' => 'Updated Club',
            'status' => 0,
        ]);
    }

    public function test_destroy_requires_authentication(): void
    {
        $club = $this->createClub();

        $this->deleteJson('/api/profile/clubs/'.$club->id)->assertUnauthorized();
    }

    public function test_destroy_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $club = $this->createClub();

        $this->deleteJson('/api/profile/clubs/'.$club->id)->assertForbidden();
    }

    public function test_destroy_deletes_club(): void
    {
        $admin = $this->actingAsAdmin();
        $club = $this->createClub($admin);

        $this->deleteJson('/api/profile/clubs/'.$club->id)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseMissing('clubs', ['id' => $club->id]);
    }
}
