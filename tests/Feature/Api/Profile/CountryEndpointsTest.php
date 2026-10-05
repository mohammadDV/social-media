<?php

namespace Tests\Feature\Api\Profile;

class CountryEndpointsTest extends ProfileCompetitionTestCase
{
    public function test_index_requires_authentication(): void
    {
        $this->getJson('/api/profile/countries')->assertUnauthorized();
    }

    public function test_index_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile/countries')->assertForbidden();
    }

    public function test_index_returns_paginated_countries_for_admin(): void
    {
        $admin = $this->actingAsAdmin();
        $country = $this->createCountry($admin, ['title' => 'Germany']);

        $this->getJson('/api/profile/countries')
            ->assertOk()
            ->assertJsonFragment(['id' => $country->id, 'title' => 'Germany']);
    }

    public function test_show_requires_authentication(): void
    {
        $country = $this->createCountry();

        $this->getJson('/api/profile/countries/'.$country->id)->assertUnauthorized();
    }

    public function test_show_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $country = $this->createCountry();

        $this->getJson('/api/profile/countries/'.$country->id)->assertForbidden();
    }

    public function test_show_returns_country_for_admin(): void
    {
        $admin = $this->actingAsAdmin();
        $country = $this->createCountry($admin, ['title' => 'Spain']);

        $this->getJson('/api/profile/countries/'.$country->id)
            ->assertOk()
            ->assertJsonPath('id', $country->id)
            ->assertJsonPath('title', 'Spain');
    }

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/api/profile/countries', [])->assertUnauthorized();
    }

    public function test_store_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/profile/countries', [
            'title' => 'France',
            'image' => 'https://example.com/fr.jpg',
            'status' => 1,
        ])->assertForbidden();
    }

    public function test_store_validation_fails(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/profile/countries', [
            'title' => 'ab',
            'status' => 3,
        ])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_store_creates_country(): void
    {
        $admin = $this->actingAsAdmin();

        $this->postJson('/api/profile/countries', [
            'title' => 'France',
            'alias_title' => 'france',
            'image' => 'https://example.com/france.jpg',
            'status' => 1,
        ])
            ->assertCreated()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('countries', [
            'title' => 'France',
            'alias_title' => 'france',
            'user_id' => $admin->id,
            'status' => 1,
        ]);
    }

    public function test_update_requires_authentication(): void
    {
        $country = $this->createCountry();

        $this->postJson('/api/profile/countries/'.$country->id, [])->assertUnauthorized();
    }

    public function test_update_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $country = $this->createCountry();

        $this->postJson('/api/profile/countries/'.$country->id, [
            'title' => 'Updated Country',
            'status' => 1,
        ])->assertForbidden();
    }

    public function test_update_validation_fails(): void
    {
        $admin = $this->actingAsAdmin();
        $country = $this->createCountry($admin);

        $this->postJson('/api/profile/countries/'.$country->id, [
            'title' => 'ab',
            'status' => 7,
        ])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_update_modifies_country(): void
    {
        $admin = $this->actingAsAdmin();
        $country = $this->createCountry($admin, ['title' => 'Old Country']);

        $this->postJson('/api/profile/countries/'.$country->id, [
            'title' => 'Updated Country',
            'alias_title' => 'updated-country',
            'image' => 'https://example.com/updated-country.jpg',
            'status' => 0,
        ])
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('countries', [
            'id' => $country->id,
            'title' => 'Updated Country',
            'status' => 0,
        ]);
    }

    public function test_destroy_requires_authentication(): void
    {
        $country = $this->createCountry();

        $this->deleteJson('/api/profile/countries/'.$country->id)->assertUnauthorized();
    }

    public function test_destroy_forbidden_without_permission(): void
    {
        $this->actingAsUser();
        $country = $this->createCountry();

        $this->deleteJson('/api/profile/countries/'.$country->id)->assertForbidden();
    }

    public function test_destroy_deletes_country(): void
    {
        $admin = $this->actingAsAdmin();
        $country = $this->createCountry($admin);

        $this->deleteJson('/api/profile/countries/'.$country->id)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseMissing('countries', ['id' => $country->id]);
    }
}
