<?php

namespace Tests\Feature\Api\Social;

use App\Models\Country;
use App\Models\Sport;
use App\Models\TicketSubject;

class CatalogEndpointsTest extends SocialTestCase
{
    public function test_sport_index_returns_sports(): void
    {
        $auth = $this->actingAsUser();

        $sport = Sport::factory()->create([
            'title' => 'Basketball',
            'alias_title' => 'basketball',
            'user_id' => $auth->id,
            'status' => 1,
        ]);

        $this->getJson('/api/sport/index')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $sport->id,
                'title' => 'Basketball',
            ]);
    }

    public function test_country_index_returns_countries(): void
    {
        $auth = $this->actingAsUser();

        $country = Country::factory()->create([
            'title' => 'Germany',
            'alias_title' => 'germany',
            'user_id' => $auth->id,
            'status' => 1,
        ]);

        $this->getJson('/api/country/index')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $country->id,
                'title' => 'Germany',
            ]);
    }

    public function test_ticket_subjects_index_returns_active_subjects(): void
    {
        $auth = $this->actingAsUser();

        $active = TicketSubject::factory()->create([
            'title' => 'Billing',
            'user_id' => $auth->id,
            'status' => 1,
        ]);
        TicketSubject::factory()->create([
            'title' => 'Inactive subject',
            'user_id' => $auth->id,
            'status' => 0,
        ]);

        $response = $this->getJson('/api/ticket-subjects/index');

        $response->assertOk();
        $ids = collect($response->json())->pluck('id');
        $this->assertTrue($ids->contains($active->id));
        $this->assertCount(1, $response->json());
    }
}
