<?php

namespace Tests\Feature\Api\Profile;

class PlayerEndpointsTest extends ProfileCompetitionTestCase
{
    public function test_all_requires_authentication(): void
    {
        $this->getJson('/api/profile/players/all')->assertUnauthorized();
    }

    public function test_all_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile/players/all')->assertForbidden();
    }

    public function test_all_returns_players_for_admin(): void
    {
        $admin = $this->actingAsAdmin();
        $sport = $this->createSport($admin);
        $country = $this->createCountry($admin);
        $player = $this->createPlayer($sport, $country, null, ['title' => 'All Player']);

        $this->getJson('/api/profile/players/all/'.$sport->id.'/'.$country->id)
            ->assertOk()
            ->assertJsonFragment(['id' => $player->id, 'title' => 'All Player']);
    }

    public function test_index_requires_authentication(): void
    {
        $this->getJson('/api/profile/players')->assertUnauthorized();
    }

    public function test_index_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile/players')->assertForbidden();
    }

    public function test_index_returns_paginated_players_for_admin(): void
    {
        $admin = $this->actingAsAdmin();
        $player = $this->createPlayer(null, null, null, ['title' => 'Paged Player']);

        $this->getJson('/api/profile/players')
            ->assertOk()
            ->assertJsonFragment(['id' => $player->id, 'title' => 'Paged Player']);
    }

    public function test_show_requires_authentication(): void
    {
        $player = $this->createPlayer();

        $this->getJson('/api/profile/players/'.$player->id)->assertUnauthorized();
    }

    public function test_show_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $player = $this->createPlayer();

        $this->getJson('/api/profile/players/'.$player->id)->assertForbidden();
    }

    public function test_show_returns_player_for_admin(): void
    {
        $admin = $this->actingAsAdmin();
        $player = $this->createPlayer(null, null, null, ['title' => 'Show Player']);

        $this->getJson('/api/profile/players/'.$player->id)
            ->assertOk()
            ->assertJsonPath('id', $player->id)
            ->assertJsonPath('title', 'Show Player');
    }

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/api/profile/players', [])->assertUnauthorized();
    }

    public function test_store_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $sport = $this->createSport();
        $country = $this->createCountry();

        $this->postJson('/api/profile/players', [
            'title' => 'New Player',
            'sport_id' => $sport->id,
            'country_id' => $country->id,
            'position' => 'Forward',
        ])->assertForbidden();
    }

    public function test_store_validation_fails(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/profile/players', [
            'title' => '',
            'position' => 'Striker',
        ])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_store_creates_player(): void
    {
        $admin = $this->actingAsAdmin();
        $sport = $this->createSport($admin);
        $country = $this->createCountry($admin);
        $club = $this->createClub($admin, $sport, $country);

        $this->postJson('/api/profile/players', [
            'title' => 'Created Player',
            'alias_title' => 'created-player',
            'sport_id' => $sport->id,
            'country_id' => $country->id,
            'club_id' => $club->id,
            'position' => 'Midfielder',
            'image' => 'https://example.com/player-created.jpg',
        ])
            ->assertCreated()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('players', [
            'title' => 'Created Player',
            'sport_id' => $sport->id,
            'country_id' => $country->id,
            'club_id' => $club->id,
            'position' => 'Midfielder',
        ]);
    }

    public function test_update_requires_authentication(): void
    {
        $player = $this->createPlayer();

        $this->postJson('/api/profile/players/'.$player->id, [])->assertUnauthorized();
    }

    public function test_update_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $player = $this->createPlayer();

        $this->postJson('/api/profile/players/'.$player->id, [
            'title' => 'Updated Player',
            'sport_id' => $player->sport_id,
            'country_id' => $player->country_id,
            'position' => 'Defender',
        ])->assertForbidden();
    }

    public function test_update_validation_fails(): void
    {
        $admin = $this->actingAsAdmin();
        $player = $this->createPlayer();

        $this->postJson('/api/profile/players/'.$player->id, [
            'title' => 'Updated Player',
            'position' => 'Invalid',
        ])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_update_modifies_player(): void
    {
        $admin = $this->actingAsAdmin();
        $player = $this->createPlayer(null, null, null, ['title' => 'Old Player']);

        $this->postJson('/api/profile/players/'.$player->id, [
            'title' => 'Updated Player',
            'alias_title' => 'updated-player',
            'sport_id' => $player->sport_id,
            'country_id' => $player->country_id,
            'club_id' => $player->club_id,
            'position' => 'Goalkeeper',
            'image' => 'https://example.com/updated-player.jpg',
        ])
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('players', [
            'id' => $player->id,
            'title' => 'Updated Player',
            'position' => 'Goalkeeper',
        ]);
    }

    public function test_destroy_requires_authentication(): void
    {
        $player = $this->createPlayer();

        $this->deleteJson('/api/profile/players/'.$player->id)->assertUnauthorized();
    }

    public function test_destroy_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $player = $this->createPlayer();

        $this->deleteJson('/api/profile/players/'.$player->id)->assertForbidden();
    }

    public function test_destroy_deletes_player(): void
    {
        $admin = $this->actingAsAdmin();
        $player = $this->createPlayer();

        $this->deleteJson('/api/profile/players/'.$player->id)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseMissing('players', ['id' => $player->id]);
    }
}
