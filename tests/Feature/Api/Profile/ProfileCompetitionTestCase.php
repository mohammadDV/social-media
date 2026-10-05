<?php

namespace Tests\Feature\Api\Profile;

use App\Models\Club;
use App\Models\Country;
use App\Models\League;
use App\Models\Live;
use App\Models\Matches;
use App\Models\Player;
use App\Models\Sport;
use App\Models\Step;
use App\Models\User;
use Tests\TestCase;

abstract class ProfileCompetitionTestCase extends TestCase
{
    protected function createSport(?User $user = null, array $attributes = []): Sport
    {
        $user ??= User::factory()->create(['status' => 1, 'password' => bcrypt('password')]);

        return Sport::query()->create(array_merge([
            'title' => 'Football',
            'alias_title' => 'football',
            'status' => 1,
            'image' => 'https://example.com/sport.jpg',
            'user_id' => $user->id,
        ], $attributes));
    }

    protected function createCountry(?User $user = null, array $attributes = []): Country
    {
        $user ??= User::factory()->create(['status' => 1, 'password' => bcrypt('password')]);

        return Country::query()->create(array_merge([
            'title' => 'Iran',
            'alias_title' => 'iran',
            'status' => 1,
            'image' => 'https://example.com/country.jpg',
            'user_id' => $user->id,
        ], $attributes));
    }

    protected function createClub(?User $user = null, ?Sport $sport = null, ?Country $country = null, array $attributes = []): Club
    {
        $user ??= User::factory()->create(['status' => 1, 'password' => bcrypt('password')]);
        $sport ??= $this->createSport($user);
        $country ??= $this->createCountry($user);

        return Club::query()->create(array_merge([
            'title' => 'Test Club',
            'alias_title' => 'test-club',
            'sport_id' => $sport->id,
            'country_id' => $country->id,
            'status' => 1,
            'image' => 'https://example.com/club.jpg',
            'user_id' => $user->id,
        ], $attributes));
    }

    protected function createLeague(?User $user = null, ?Sport $sport = null, ?Country $country = null, array $attributes = []): League
    {
        $user ??= User::factory()->create(['status' => 1, 'password' => bcrypt('password')]);
        $sport ??= $this->createSport($user);
        $country ??= $this->createCountry($user);

        return League::query()->create(array_merge([
            'title' => 'Premier League',
            'alias_title' => 'premier-league',
            'sport_id' => $sport->id,
            'country_id' => $country->id,
            'status' => 1,
            'type' => 1,
            'priority' => 1,
            'image' => 'https://example.com/league.jpg',
            'user_id' => $user->id,
        ], $attributes));
    }

    protected function createStep(?User $user = null, ?League $league = null, array $attributes = []): Step
    {
        $user ??= User::factory()->create(['status' => 1, 'password' => bcrypt('password')]);
        $league ??= $this->createLeague($user);

        return Step::query()->create(array_merge([
            'title' => 'Week 1',
            'league_id' => $league->id,
            'user_id' => $user->id,
            'priority' => 1,
            'current' => 1,
            'status' => 1,
        ], $attributes));
    }

    protected function createMatch(?User $user = null, ?Step $step = null, ?Club $home = null, ?Club $away = null, array $attributes = []): Matches
    {
        $user ??= User::factory()->create(['status' => 1, 'password' => bcrypt('password')]);
        $step ??= $this->createStep($user);
        $home ??= $this->createClub($user, null, null, ['title' => 'Home Club', 'alias_title' => 'home-club']);
        $away ??= $this->createClub($user, Sport::query()->find($home->sport_id), Country::query()->find($home->country_id), [
            'title' => 'Away Club',
            'alias_title' => 'away-club',
        ]);

        return Matches::query()->create(array_merge([
            'home_id' => $home->id,
            'away_id' => $away->id,
            'hsc' => '0',
            'asc' => '0',
            'date' => '2026-10-05 18:00',
            'step_id' => $step->id,
            'user_id' => $user->id,
            'priority' => 1,
            'status' => 0,
        ], $attributes));
    }

    protected function createLive(?User $user = null, array $attributes = []): Live
    {
        $user ??= User::factory()->create(['status' => 1, 'password' => bcrypt('password')]);

        return Live::query()->create(array_merge([
            'title' => 'Live Match Night',
            'teams' => 'Team A - Team B',
            'date' => '2026-10-05 20:00',
            'link' => 'https://example.com/live',
            'info' => 'Main broadcast',
            'status' => 1,
            'priority' => 1,
            'user_id' => $user->id,
        ], $attributes));
    }

    protected function createPlayer(?Sport $sport = null, ?Country $country = null, ?Club $club = null, array $attributes = []): Player
    {
        $owner = User::factory()->create(['status' => 1, 'password' => bcrypt('password')]);
        $sport ??= $this->createSport($owner);
        $country ??= $this->createCountry($owner);
        $club ??= $this->createClub($owner, $sport, $country);

        return Player::query()->create(array_merge([
            'title' => 'Test Player',
            'alias_title' => 'test-player',
            'sport_id' => $sport->id,
            'country_id' => $country->id,
            'club_id' => $club->id,
            'position' => 'Forward',
            'image' => 'https://example.com/player.jpg',
        ], $attributes));
    }
}
