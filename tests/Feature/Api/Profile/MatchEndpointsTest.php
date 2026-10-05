<?php

namespace Tests\Feature\Api\Profile;

class MatchEndpointsTest extends ProfileCompetitionTestCase
{
    public function test_show_requires_authentication(): void
    {
        $match = $this->createMatch();

        $this->getJson('/api/profile/matches/'.$match->id)->assertUnauthorized();
    }

    public function test_show_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $match = $this->createMatch();

        $this->getJson('/api/profile/matches/'.$match->id)->assertForbidden();
    }

    public function test_show_returns_match_for_admin(): void
    {
        $admin = $this->actingAsAdmin();
        $match = $this->createMatch($admin, null, null, null, ['hsc' => '3', 'asc' => '2']);

        $this->getJson('/api/profile/matches/'.$match->id)
            ->assertOk()
            ->assertJsonPath('id', $match->id)
            ->assertJsonPath('hsc', '3')
            ->assertJsonPath('asc', '2');
    }

    public function test_store_requires_authentication(): void
    {
        $step = $this->createStep();

        $this->postJson('/api/profile/steps/'.$step->id.'/matches', [])->assertUnauthorized();
    }

    public function test_store_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $step = $this->createStep();
        $home = $this->createClub();
        $away = $this->createClub(null, null, null, ['title' => 'Away', 'alias_title' => 'away']);

        $this->postJson('/api/profile/steps/'.$step->id.'/matches', [
            'home_id' => $home->id,
            'away_id' => $away->id,
            'hsc' => '0',
            'asc' => '0',
            'date' => '2026-10-05 18:00',
            'priority' => 1,
        ])->assertForbidden();
    }

    public function test_store_validation_fails(): void
    {
        $admin = $this->actingAsAdmin();
        $step = $this->createStep($admin);

        $this->postJson('/api/profile/steps/'.$step->id.'/matches', [
            'home_id' => 999999,
            'away_id' => 999998,
            'hsc' => '',
            'priority' => 5000,
        ])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_store_creates_match(): void
    {
        $admin = $this->actingAsAdmin();
        $step = $this->createStep($admin);
        $home = $this->createClub($admin, null, null, ['title' => 'Home FC', 'alias_title' => 'home-fc']);
        $away = $this->createClub($admin, null, null, ['title' => 'Away FC', 'alias_title' => 'away-fc']);

        $this->postJson('/api/profile/steps/'.$step->id.'/matches', [
            'home_id' => $home->id,
            'away_id' => $away->id,
            'hsc' => '1',
            'asc' => '0',
            'link' => 'https://example.com/match',
            'date' => '2026-10-06 19:00',
            'priority' => 2,
            'status' => 0,
        ])
            ->assertCreated()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('matches', [
            'home_id' => $home->id,
            'away_id' => $away->id,
            'hsc' => '1',
            'asc' => '0',
            'step_id' => $step->id,
            'user_id' => $admin->id,
            'priority' => 2,
            'status' => 0,
        ]);
    }

    public function test_update_requires_authentication(): void
    {
        $step = $this->createStep();
        $match = $this->createMatch(null, $step);

        $this->postJson('/api/profile/steps/'.$step->id.'/matches/'.$match->id, [])->assertUnauthorized();
    }

    public function test_update_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $step = $this->createStep();
        $match = $this->createMatch(null, $step);

        $this->postJson('/api/profile/steps/'.$step->id.'/matches/'.$match->id, [
            'home_id' => $match->home_id,
            'away_id' => $match->away_id,
            'hsc' => '2',
            'asc' => '2',
            'date' => '2026-10-07 20:00',
            'priority' => 1,
        ])->assertForbidden();
    }

    public function test_update_validation_fails(): void
    {
        $admin = $this->actingAsAdmin();
        $step = $this->createStep($admin);
        $match = $this->createMatch($admin, $step);

        $this->postJson('/api/profile/steps/'.$step->id.'/matches/'.$match->id, [
            'home_id' => 999999,
            'away_id' => 999998,
            'priority' => 9999,
        ])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_update_modifies_match(): void
    {
        $admin = $this->actingAsAdmin();
        $step = $this->createStep($admin);
        $match = $this->createMatch($admin, $step);

        $this->postJson('/api/profile/steps/'.$step->id.'/matches/'.$match->id, [
            'home_id' => $match->home_id,
            'away_id' => $match->away_id,
            'hsc' => '4',
            'asc' => '1',
            'link' => 'https://example.com/updated-match',
            'date' => '2026-10-08 21:00',
            'priority' => 8,
            'status' => 1,
        ])
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('matches', [
            'id' => $match->id,
            'hsc' => '4',
            'asc' => '1',
            'priority' => 8,
            'date' => '2026-10-08 21:00',
            'status' => 1,
        ]);
    }

    public function test_destroy_requires_authentication(): void
    {
        $match = $this->createMatch();

        $this->deleteJson('/api/profile/matches/'.$match->id)->assertUnauthorized();
    }

    public function test_destroy_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $match = $this->createMatch();

        $this->deleteJson('/api/profile/matches/'.$match->id)->assertForbidden();
    }

    public function test_destroy_deletes_match(): void
    {
        $admin = $this->actingAsAdmin();
        $match = $this->createMatch($admin);

        $this->deleteJson('/api/profile/matches/'.$match->id)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseMissing('matches', ['id' => $match->id]);
    }
}
