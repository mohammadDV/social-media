<?php

namespace Tests\Feature\Api\Profile;

use Illuminate\Support\Facades\DB;

class LeagueEndpointsTest extends ProfileCompetitionTestCase
{
    public function test_index_requires_authentication(): void
    {
        $this->getJson('/api/profile/leagues')->assertUnauthorized();
    }

    public function test_index_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile/leagues')->assertForbidden();
    }

    public function test_index_returns_paginated_leagues_for_admin(): void
    {
        $admin = $this->actingAsAdmin();
        $league = $this->createLeague($admin, null, null, ['title' => 'Paged League']);

        $this->getJson('/api/profile/leagues')
            ->assertOk()
            ->assertJsonFragment(['id' => $league->id, 'title' => 'Paged League']);
    }

    public function test_get_table_league_requires_authentication(): void
    {
        $this->getJson('/api/profile/leagues/tables')->assertUnauthorized();
    }

    public function test_get_table_league_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile/leagues/tables')->assertForbidden();
    }

    public function test_get_table_league_returns_config_tables(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/profile/leagues/tables')->assertOk();

        $this->assertNotEmpty($response->json());
        $this->assertArrayHasKey('id', $response->json()[0]);
        $this->assertArrayHasKey('sport_id', $response->json()[0]);
    }

    public function test_show_requires_authentication(): void
    {
        $league = $this->createLeague();

        $this->getJson('/api/profile/leagues/'.$league->id)->assertUnauthorized();
    }

    public function test_show_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $league = $this->createLeague();

        $this->getJson('/api/profile/leagues/'.$league->id)->assertForbidden();
    }

    public function test_show_returns_league_for_admin(): void
    {
        $admin = $this->actingAsAdmin();
        $league = $this->createLeague($admin, null, null, ['title' => 'Show League']);

        $this->getJson('/api/profile/leagues/'.$league->id)
            ->assertOk()
            ->assertJsonPath('id', $league->id)
            ->assertJsonPath('title', 'Show League');
    }

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/api/profile/leagues', [])->assertUnauthorized();
    }

    public function test_store_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $sport = $this->createSport();
        $country = $this->createCountry();

        $this->postJson('/api/profile/leagues', [
            'title' => 'New League',
            'sport_id' => $sport->id,
            'country_id' => $country->id,
            'image' => 'https://example.com/l.jpg',
            'status' => 1,
            'type' => 1,
        ])->assertForbidden();
    }

    public function test_store_validation_fails(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/profile/leagues', [
            'title' => 'ab',
            'type' => 9,
            'status' => 5,
        ])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_store_creates_league(): void
    {
        $admin = $this->actingAsAdmin();
        $sport = $this->createSport($admin);
        $country = $this->createCountry($admin);

        $this->postJson('/api/profile/leagues', [
            'title' => 'Created League',
            'alias_title' => 'created-league',
            'sport_id' => $sport->id,
            'country_id' => $country->id,
            'image' => 'https://example.com/created-league.jpg',
            'status' => 1,
            'type' => 1,
            'priority' => 5,
        ])
            ->assertCreated()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('leagues', [
            'title' => 'Created League',
            'sport_id' => $sport->id,
            'country_id' => $country->id,
            'user_id' => $admin->id,
            'type' => 1,
            'status' => 1,
        ]);
    }

    public function test_update_requires_authentication(): void
    {
        $league = $this->createLeague();

        $this->postJson('/api/profile/leagues/'.$league->id, [])->assertUnauthorized();
    }

    public function test_update_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $league = $this->createLeague();

        $this->postJson('/api/profile/leagues/'.$league->id, [
            'title' => 'Updated League',
            'sport_id' => $league->sport_id,
            'country_id' => $league->country_id,
            'status' => 1,
            'type' => 1,
        ])->assertForbidden();
    }

    public function test_update_validation_fails(): void
    {
        $admin = $this->actingAsAdmin();
        $league = $this->createLeague($admin);

        $this->postJson('/api/profile/leagues/'.$league->id, [
            'title' => 'ab',
            'type' => 5,
            'status' => 1,
        ])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_update_modifies_league(): void
    {
        $admin = $this->actingAsAdmin();
        $league = $this->createLeague($admin, null, null, ['title' => 'Old League']);

        $this->postJson('/api/profile/leagues/'.$league->id, [
            'title' => 'Updated League',
            'alias_title' => 'updated-league',
            'sport_id' => $league->sport_id,
            'country_id' => $league->country_id,
            'image' => 'https://example.com/updated-league.jpg',
            'status' => 0,
            'type' => 2,
            'priority' => 10,
        ])
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('leagues', [
            'id' => $league->id,
            'title' => 'Updated League',
            'type' => 2,
            'status' => 0,
        ]);
    }

    public function test_destroy_requires_authentication(): void
    {
        $league = $this->createLeague();

        $this->deleteJson('/api/profile/leagues/'.$league->id)->assertUnauthorized();
    }

    public function test_destroy_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $league = $this->createLeague();

        $this->deleteJson('/api/profile/leagues/'.$league->id)->assertForbidden();
    }

    public function test_destroy_deletes_league(): void
    {
        $admin = $this->actingAsAdmin();
        $league = $this->createLeague($admin);

        $this->deleteJson('/api/profile/leagues/'.$league->id)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseMissing('leagues', ['id' => $league->id]);
    }

    public function test_get_clubs_requires_authentication(): void
    {
        $league = $this->createLeague();

        $this->getJson('/api/profile/leagues/'.$league->id.'/clubs')->assertUnauthorized();
    }

    public function test_get_clubs_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $league = $this->createLeague();

        $this->getJson('/api/profile/leagues/'.$league->id.'/clubs')->assertForbidden();
    }

    public function test_get_clubs_returns_league_clubs(): void
    {
        $admin = $this->actingAsAdmin();
        $league = $this->createLeague($admin);
        $club = $this->createClub($admin, null, null, ['title' => 'League Club']);

        DB::table('club_league')->insert([
            'league_id' => $league->id,
            'club_id' => $club->id,
            'points' => 6,
            'games_count' => 2,
        ]);

        $this->getJson('/api/profile/leagues/'.$league->id.'/clubs')
            ->assertOk()
            ->assertJsonFragment(['id' => $club->id, 'title' => 'League Club']);
    }

    public function test_store_clubs_requires_authentication(): void
    {
        $league = $this->createLeague();

        $this->postJson('/api/profile/leagues/'.$league->id.'/clubs', [])->assertUnauthorized();
    }

    public function test_store_clubs_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $league = $this->createLeague();
        $club = $this->createClub();

        $this->postJson('/api/profile/leagues/'.$league->id.'/clubs', [
            $club->id => ['points' => 3, 'games_count' => 1],
        ])->assertForbidden();
    }

    public function test_store_clubs_validation_fails(): void
    {
        $admin = $this->actingAsAdmin();
        $league = $this->createLeague($admin);

        $this->postJson('/api/profile/leagues/'.$league->id.'/clubs', [
            'clubs' => [
                ['club_id' => 'bad', 'points' => -1, 'games_count' => 'x'],
            ],
        ])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_store_clubs_syncs_clubs_to_league(): void
    {
        $admin = $this->actingAsAdmin();
        $league = $this->createLeague($admin);
        $club = $this->createClub($admin);

        $this->postJson('/api/profile/leagues/'.$league->id.'/clubs', [
            $club->id => [
                'points' => 9,
                'games_count' => 3,
            ],
        ])
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('club_league', [
            'league_id' => $league->id,
            'club_id' => $club->id,
            'points' => 9,
            'games_count' => 3,
        ]);
    }

    public function test_get_all_steps_requires_authentication(): void
    {
        $league = $this->createLeague();

        $this->getJson('/api/profile/leagues/'.$league->id.'/steps')->assertUnauthorized();
    }

    public function test_get_all_steps_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $league = $this->createLeague();

        $this->getJson('/api/profile/leagues/'.$league->id.'/steps')->assertForbidden();
    }

    public function test_get_all_steps_returns_steps(): void
    {
        $admin = $this->actingAsAdmin();
        $league = $this->createLeague($admin);
        $step = $this->createStep($admin, $league, ['title' => 'Round 1']);

        $this->getJson('/api/profile/leagues/'.$league->id.'/steps')
            ->assertOk()
            ->assertJsonFragment(['id' => $step->id, 'title' => 'Round 1']);
    }
}
