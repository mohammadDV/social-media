<?php

namespace Tests\Feature\Api\Social;

use App\Models\Club;
use App\Models\Country;
use App\Models\FavoriteClub;
use App\Models\Sport;

class MemberEndpointsTest extends SocialTestCase
{
    public function test_new_members_returns_other_active_users(): void
    {
        $auth = $this->actingAsUser(['nickname' => 'auth-member']);
        $newbie = $this->makeUser(['nickname' => 'new-member']);

        $response = $this->jsonAs($auth, 'GET', '/api/new-members');

        $response->assertOk();

        $ids = collect($response->json())->pluck('id');
        $this->assertTrue($ids->contains($newbie->id));
        $this->assertFalse($ids->contains($auth->id));
    }

    public function test_congenial_members_returns_users_sharing_favorite_clubs(): void
    {
        $auth = $this->actingAsUser(['nickname' => 'auth-cong']);
        $peer = $this->makeUser(['nickname' => 'peer-cong']);
        $stranger = $this->makeUser(['nickname' => 'stranger']);

        $country = Country::factory()->create([
            'title' => 'Iran',
            'user_id' => $auth->id,
            'status' => 1,
        ]);
        $sport = Sport::factory()->create([
            'title' => 'Football',
            'user_id' => $auth->id,
            'status' => 1,
        ]);
        $club = Club::factory()->create([
            'title' => 'Shared Club',
            'sport_id' => $sport->id,
            'country_id' => $country->id,
            'status' => 1,
            'user_id' => $auth->id,
        ]);

        FavoriteClub::create(['user_id' => $auth->id, 'club_id' => $club->id]);
        FavoriteClub::create(['user_id' => $peer->id, 'club_id' => $club->id]);

        $response = $this->jsonAs($auth, 'GET', '/api/congenial-members');

        $response->assertOk();

        $ids = collect($response->json())->pluck('id');
        $this->assertTrue($ids->contains($peer->id));
        $this->assertFalse($ids->contains($stranger->id));
        $this->assertFalse($ids->contains($auth->id));
    }
}
