<?php

namespace Tests\Feature\Api\Profile;

use Illuminate\Support\Facades\DB;

class StepEndpointsTest extends ProfileCompetitionTestCase
{
    public function test_show_requires_authentication(): void
    {
        $step = $this->createStep();

        $this->getJson('/api/profile/steps/'.$step->id)->assertUnauthorized();
    }

    public function test_show_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $step = $this->createStep();

        $this->getJson('/api/profile/steps/'.$step->id)->assertForbidden();
    }

    public function test_show_returns_step_for_admin(): void
    {
        $admin = $this->actingAsAdmin();
        $step = $this->createStep($admin, null, ['title' => 'Show Step']);

        $this->getJson('/api/profile/steps/'.$step->id)
            ->assertOk()
            ->assertJsonPath('id', $step->id)
            ->assertJsonPath('title', 'Show Step');
    }

    public function test_get_step_info_requires_authentication(): void
    {
        $step = $this->createStep();

        $this->getJson('/api/profile/steps/'.$step->id.'/info')->assertUnauthorized();
    }

    public function test_get_step_info_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $step = $this->createStep();

        $this->getJson('/api/profile/steps/'.$step->id.'/info')->assertForbidden();
    }

    public function test_get_step_info_returns_matches_and_clubs(): void
    {
        $admin = $this->actingAsAdmin();
        $league = $this->createLeague($admin);
        $step = $this->createStep($admin, $league);
        $club = $this->createClub($admin);

        DB::table('club_league')->insert([
            'league_id' => $league->id,
            'club_id' => $club->id,
            'points' => 3,
            'games_count' => 1,
        ]);

        $match = $this->createMatch($admin, $step);

        $this->getJson('/api/profile/steps/'.$step->id.'/info')
            ->assertOk()
            ->assertJsonStructure(['matches', 'clubs']);
    }

    public function test_create_requires_authentication(): void
    {
        $league = $this->createLeague();

        $this->getJson('/api/profile/steps/create/'.$league->id)->assertUnauthorized();
    }

    public function test_create_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $league = $this->createLeague();

        $this->getJson('/api/profile/steps/create/'.$league->id)->assertForbidden();
    }

    public function test_store_requires_authentication(): void
    {
        $league = $this->createLeague();

        $this->postJson('/api/profile/leagues/'.$league->id.'/steps/', [])->assertUnauthorized();
    }

    public function test_store_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $league = $this->createLeague();

        $this->postJson('/api/profile/leagues/'.$league->id.'/steps/', [
            'title' => 'New Step',
            'priority' => 1,
            'current' => 1,
            'status' => 1,
        ])->assertForbidden();
    }

    public function test_store_validation_fails(): void
    {
        $admin = $this->actingAsAdmin();
        $league = $this->createLeague($admin);

        $this->postJson('/api/profile/leagues/'.$league->id.'/steps/', [
            'title' => '',
            'priority' => 999,
            'current' => 5,
            'status' => 5,
        ])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_store_creates_step(): void
    {
        $admin = $this->actingAsAdmin();
        $league = $this->createLeague($admin);

        $this->postJson('/api/profile/leagues/'.$league->id.'/steps/', [
            'title' => 'Created Step',
            'priority' => 2,
            'current' => 1,
            'status' => 1,
        ])
            ->assertCreated()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('steps', [
            'title' => 'Created Step',
            'league_id' => $league->id,
            'user_id' => $admin->id,
            'current' => 1,
            'status' => 1,
        ]);
    }

    public function test_update_requires_authentication(): void
    {
        $league = $this->createLeague();
        $step = $this->createStep(null, $league);

        $this->postJson('/api/profile/leagues/'.$league->id.'/steps/'.$step->id, [])->assertUnauthorized();
    }

    public function test_update_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $league = $this->createLeague();
        $step = $this->createStep(null, $league);

        $this->postJson('/api/profile/leagues/'.$league->id.'/steps/'.$step->id, [
            'title' => 'Updated Step',
            'priority' => 1,
            'current' => 1,
            'status' => 1,
        ])->assertForbidden();
    }

    public function test_update_validation_fails(): void
    {
        $admin = $this->actingAsAdmin();
        $league = $this->createLeague($admin);
        $step = $this->createStep($admin, $league);

        $this->postJson('/api/profile/leagues/'.$league->id.'/steps/'.$step->id, [
            'title' => '',
            'priority' => 500,
            'current' => 3,
            'status' => 3,
        ])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_update_modifies_step(): void
    {
        $admin = $this->actingAsAdmin();
        $league = $this->createLeague($admin);
        $step = $this->createStep($admin, $league, ['title' => 'Old Step']);

        $this->postJson('/api/profile/leagues/'.$league->id.'/steps/'.$step->id, [
            'title' => 'Updated Step',
            'priority' => 4,
            'current' => 1,
            'status' => 0,
        ])
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('steps', [
            'id' => $step->id,
            'title' => 'Updated Step',
            'priority' => 4,
            'status' => 0,
        ]);
    }

    public function test_destroy_requires_authentication(): void
    {
        $step = $this->createStep();

        $this->deleteJson('/api/profile/steps/'.$step->id)->assertUnauthorized();
    }

    public function test_destroy_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $step = $this->createStep();

        $this->deleteJson('/api/profile/steps/'.$step->id)->assertForbidden();
    }

    public function test_destroy_deletes_step(): void
    {
        $admin = $this->actingAsAdmin();
        $step = $this->createStep($admin);

        $this->deleteJson('/api/profile/steps/'.$step->id)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseMissing('steps', ['id' => $step->id]);
    }

    public function test_get_all_clubs_requires_authentication(): void
    {
        $step = $this->createStep();

        $this->getJson('/api/profile/steps/'.$step->id.'/clubs')->assertUnauthorized();
    }

    public function test_get_all_clubs_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $step = $this->createStep();

        $this->getJson('/api/profile/steps/'.$step->id.'/clubs')->assertForbidden();
    }

    public function test_get_all_clubs_returns_step_clubs(): void
    {
        $admin = $this->actingAsAdmin();
        $step = $this->createStep($admin);
        $club = $this->createClub($admin, null, null, ['title' => 'Step Club']);

        DB::table('club_step')->insert([
            'step_id' => $step->id,
            'club_id' => $club->id,
            'points' => 4,
            'games_count' => 2,
        ]);

        $this->getJson('/api/profile/steps/'.$step->id.'/clubs')
            ->assertOk()
            ->assertJsonFragment(['id' => $club->id, 'title' => 'Step Club']);
    }

    public function test_store_clubs_requires_authentication(): void
    {
        $step = $this->createStep();

        $this->postJson('/api/profile/steps/'.$step->id.'/clubs', [])->assertUnauthorized();
    }

    public function test_store_clubs_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $step = $this->createStep();
        $club = $this->createClub();

        $this->postJson('/api/profile/steps/'.$step->id.'/clubs', [
            $club->id => ['points' => 1, 'games_count' => 1],
        ])->assertForbidden();
    }

    public function test_store_clubs_validation_fails(): void
    {
        $admin = $this->actingAsAdmin();
        $step = $this->createStep($admin);

        $this->postJson('/api/profile/steps/'.$step->id.'/clubs', [
            'clubs' => [
                ['club_id' => 'bad', 'points' => -2, 'games_count' => 'no'],
            ],
        ])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_store_clubs_syncs_clubs_to_step(): void
    {
        $admin = $this->actingAsAdmin();
        $step = $this->createStep($admin);
        $club = $this->createClub($admin);

        $this->postJson('/api/profile/steps/'.$step->id.'/clubs', [
            $club->id => [
                'points' => 7,
                'games_count' => 4,
            ],
        ])
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('club_step', [
            'step_id' => $step->id,
            'club_id' => $club->id,
            'points' => 7,
            'games_count' => 4,
        ]);
    }

    public function test_get_all_matches_requires_authentication(): void
    {
        $step = $this->createStep();

        $this->getJson('/api/profile/steps/'.$step->id.'/matches')->assertUnauthorized();
    }

    public function test_get_all_matches_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $step = $this->createStep();

        $this->getJson('/api/profile/steps/'.$step->id.'/matches')->assertForbidden();
    }

    public function test_get_all_matches_returns_matches(): void
    {
        $admin = $this->actingAsAdmin();
        $step = $this->createStep($admin);
        $match = $this->createMatch($admin, $step, null, null, ['hsc' => '2', 'asc' => '1']);

        $this->getJson('/api/profile/steps/'.$step->id.'/matches')
            ->assertOk()
            ->assertJsonFragment(['id' => $match->id, 'hsc' => '2', 'asc' => '1']);
    }
}
