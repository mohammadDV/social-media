<?php

namespace Tests\Feature\Api\Social;

use App\Models\Club;
use App\Models\Country;
use App\Models\FavoriteClub;
use App\Models\Sport;

class FavoriteEndpointsTest extends SocialTestCase
{
    private function createActiveClub(int $userId, string $title = 'Test Club'): Club
    {
        $country = Country::factory()->create([
            'title' => 'Iran',
            'user_id' => $userId,
            'status' => 1,
        ]);
        $sport = Sport::factory()->create([
            'title' => 'Football',
            'user_id' => $userId,
            'status' => 1,
        ]);

        return Club::factory()->create([
            'title' => $title,
            'alias_title' => $title,
            'sport_id' => $sport->id,
            'country_id' => $country->id,
            'status' => 1,
            'user_id' => $userId,
        ]);
    }

    public function test_get_favorite_clubs_for_auth_user(): void
    {
        $auth = $this->actingAsUser();
        $club = $this->createActiveClub($auth->id, 'Auth Club');

        FavoriteClub::create([
            'user_id' => $auth->id,
            'club_id' => $club->id,
        ]);

        $response = $this->getJson('/api/favorite/clubs');

        $response->assertOk();
        $this->assertCount(1, $response->json());
        $this->assertSame($club->id, $response->json('0.id'));
    }

    public function test_get_favorite_clubs_for_another_user(): void
    {
        $auth = $this->actingAsUser();
        $other = $this->makeUser();
        $club = $this->createActiveClub($other->id, 'Other Club');

        FavoriteClub::create([
            'user_id' => $other->id,
            'club_id' => $club->id,
        ]);

        $response = $this->jsonAs($auth, 'GET', "/api/favorite/clubs/{$other->id}");

        $response->assertOk();
        $this->assertSame($club->id, $response->json('0.id'));
    }

    public function test_get_favorite_clubs_limited(): void
    {
        $auth = $this->actingAsUser();
        $club = $this->createActiveClub($auth->id, 'Limited Club');

        FavoriteClub::create([
            'user_id' => $auth->id,
            'club_id' => $club->id,
        ]);

        $this->getJson('/api/favorite/clubs-limited')
            ->assertOk()
            ->assertJsonFragment(['id' => $club->id]);
    }

    public function test_search_favorite_clubs(): void
    {
        $auth = $this->actingAsUser();
        $club = $this->createActiveClub($auth->id, 'Searchable FC');

        $this->postJson('/api/favorite/clubs/search', [
            'sport_id' => $club->sport_id,
            'search' => 'Searchable',
        ])
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.id', $club->id);
    }

    public function test_store_favorite_club_creates_and_toggles(): void
    {
        $auth = $this->actingAsUser();
        $club = $this->createActiveClub($auth->id, 'Toggle Club');

        $this->postJson("/api/favorite/clubs/{$club->id}")
            ->assertOk()
            ->assertJson([
                'status' => 1,
                'active' => 1,
            ]);

        $this->assertDatabaseHas('favorite_clubs', [
            'user_id' => $auth->id,
            'club_id' => $club->id,
        ]);

        $this->postJson("/api/favorite/clubs/{$club->id}")
            ->assertOk()
            ->assertJson([
                'status' => 1,
                'active' => 0,
            ]);

        $this->assertDatabaseMissing('favorite_clubs', [
            'user_id' => $auth->id,
            'club_id' => $club->id,
        ]);
    }

    public function test_search_favorite_clubs_requires_sport_id(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/favorite/clubs/search', [])
            ->assertStatus(400)
            ->assertJson(['status' => 0]);
    }
}
