<?php

namespace Tests\Feature\Api\Profile;

use App\Models\Advertise;
use App\Models\AdvertiseForm;

class AdvertiseEndpointsTest extends ProfileTestCase
{
    public function test_advertise_places_requires_auth(): void
    {
        $this->getJson('/api/profile/advertise/places')->assertUnauthorized();
    }

    public function test_advertise_places_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile/advertise/places')->assertForbidden();
    }

    public function test_advertise_places_returns_list_for_admin(): void
    {
        $this->actingAsAdmin();

        $this->getJson('/api/profile/advertise/places')
            ->assertOk()
            ->assertJsonFragment(['id' => 1]);
    }

    public function test_advertise_index_requires_auth(): void
    {
        $this->getJson('/api/profile/advertise')->assertUnauthorized();
    }

    public function test_advertise_index_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile/advertise')->assertForbidden();
    }

    public function test_advertise_index_returns_paginated_list(): void
    {
        $admin = $this->actingAsAdmin();

        Advertise::create([
            'title' => 'Banner A',
            'image' => 'https://example.com/a.jpg',
            'link' => 'https://example.com',
            'place_id' => 1,
            'status' => 1,
            'user_id' => $admin->id,
        ]);

        $this->getJson('/api/profile/advertise')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.title', 'Banner A');
    }

    public function test_advertise_show_requires_auth(): void
    {
        $advertise = Advertise::create([
            'title' => 'Banner Show',
            'image' => 'https://example.com/s.jpg',
            'place_id' => 1,
            'status' => 1,
            'user_id' => 1,
        ]);

        $this->getJson('/api/profile/advertise/'.$advertise->id)->assertUnauthorized();
    }

    public function test_advertise_show_forbidden_without_permission(): void
    {
        $admin = $this->actingAsAdmin();
        $advertise = Advertise::create([
            'title' => 'Banner Show',
            'image' => 'https://example.com/s.jpg',
            'place_id' => 1,
            'status' => 1,
            'user_id' => $admin->id,
        ]);

        $this->actingAsUser();

        $this->getJson('/api/profile/advertise/'.$advertise->id)->assertForbidden();
    }

    public function test_advertise_show_returns_advertise(): void
    {
        $admin = $this->actingAsAdmin();
        $advertise = Advertise::create([
            'title' => 'Banner Detail',
            'image' => 'https://example.com/d.jpg',
            'place_id' => 2,
            'status' => 1,
            'user_id' => $admin->id,
        ]);

        $this->getJson('/api/profile/advertise/'.$advertise->id)
            ->assertOk()
            ->assertJsonPath('id', $advertise->id)
            ->assertJsonPath('title', 'Banner Detail');
    }

    public function test_advertise_store_requires_auth(): void
    {
        $this->postJson('/api/profile/advertise', [])->assertUnauthorized();
    }

    public function test_advertise_store_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/profile/advertise', [
            'title' => 'New Banner',
            'place_id' => 1,
            'status' => 1,
            'image' => 'https://example.com/n.jpg',
        ])->assertForbidden();
    }

    public function test_advertise_store_validation_fails(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/profile/advertise', [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_advertise_store_creates_record(): void
    {
        $admin = $this->actingAsAdmin();

        $this->postJson('/api/profile/advertise', [
            'title' => 'Created Banner',
            'place_id' => 3,
            'status' => 1,
            'image' => 'https://example.com/created.jpg',
            'link' => 'https://example.com/ad',
        ])
            ->assertCreated()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('advertises', [
            'title' => 'Created Banner',
            'place_id' => 3,
            'status' => 1,
            'user_id' => $admin->id,
            'image' => 'https://example.com/created.jpg',
        ]);
    }

    public function test_advertise_update_requires_auth(): void
    {
        $advertise = Advertise::create([
            'title' => 'Old',
            'image' => 'https://example.com/o.jpg',
            'place_id' => 1,
            'status' => 0,
            'user_id' => 1,
        ]);

        $this->postJson('/api/profile/advertise/'.$advertise->id, [])->assertUnauthorized();
    }

    public function test_advertise_update_forbidden_without_permission(): void
    {
        $admin = $this->actingAsAdmin();
        $advertise = Advertise::create([
            'title' => 'Old',
            'image' => 'https://example.com/o.jpg',
            'place_id' => 1,
            'status' => 0,
            'user_id' => $admin->id,
        ]);

        $this->actingAsUser();

        $this->postJson('/api/profile/advertise/'.$advertise->id, [
            'title' => 'Updated Banner',
            'place_id' => 2,
            'status' => 1,
            'image' => 'https://example.com/u.jpg',
        ])->assertForbidden();
    }

    public function test_advertise_update_validation_fails(): void
    {
        $admin = $this->actingAsAdmin();
        $advertise = Advertise::create([
            'title' => 'Old',
            'image' => 'https://example.com/o.jpg',
            'place_id' => 1,
            'status' => 0,
            'user_id' => $admin->id,
        ]);

        $this->postJson('/api/profile/advertise/'.$advertise->id, [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_advertise_update_updates_record(): void
    {
        $admin = $this->actingAsAdmin();
        $advertise = Advertise::create([
            'title' => 'Old Banner',
            'image' => 'https://example.com/o.jpg',
            'place_id' => 1,
            'status' => 0,
            'user_id' => $admin->id,
        ]);

        $this->postJson('/api/profile/advertise/'.$advertise->id, [
            'title' => 'Updated Banner',
            'place_id' => 5,
            'status' => 1,
            'image' => 'https://example.com/updated.jpg',
            'link' => 'https://example.com/new',
        ])
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('advertises', [
            'id' => $advertise->id,
            'title' => 'Updated Banner',
            'place_id' => 5,
            'status' => 1,
            'image' => 'https://example.com/updated.jpg',
        ]);
    }

    public function test_advertise_destroy_requires_auth(): void
    {
        $advertise = Advertise::create([
            'title' => 'Delete Me',
            'image' => 'https://example.com/del.jpg',
            'place_id' => 1,
            'status' => 1,
            'user_id' => 1,
        ]);

        $this->deleteJson('/api/profile/advertise/'.$advertise->id)->assertUnauthorized();
    }

    public function test_advertise_destroy_forbidden_without_permission(): void
    {
        $admin = $this->actingAsAdmin();
        $advertise = Advertise::create([
            'title' => 'Delete Me',
            'image' => 'https://example.com/del.jpg',
            'place_id' => 1,
            'status' => 1,
            'user_id' => $admin->id,
        ]);

        $this->actingAsUser();

        $this->deleteJson('/api/profile/advertise/'.$advertise->id)->assertForbidden();
    }

    public function test_advertise_destroy_deletes_record(): void
    {
        $admin = $this->actingAsAdmin();
        $advertise = Advertise::create([
            'title' => 'Delete Me',
            'image' => 'https://example.com/del.jpg',
            'place_id' => 1,
            'status' => 1,
            'user_id' => $admin->id,
        ]);

        $this->deleteJson('/api/profile/advertise/'.$advertise->id)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseMissing('advertises', ['id' => $advertise->id]);
    }

    public function test_advertise_form_index_requires_auth(): void
    {
        $this->getJson('/api/profile/advertise-form')->assertUnauthorized();
    }

    public function test_advertise_form_index_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile/advertise-form')->assertForbidden();
    }

    public function test_advertise_form_index_returns_paginated_list(): void
    {
        $this->actingAsAdmin();

        AdvertiseForm::create([
            'first_name' => 'Ali',
            'last_name' => 'Test',
            'phone' => '09120000000',
            'content' => 'Need ad space',
            'email' => 'ali@example.com',
        ]);

        $this->getJson('/api/profile/advertise-form')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.first_name', 'Ali');
    }

    public function test_advertise_form_destroy_requires_auth(): void
    {
        $form = AdvertiseForm::create([
            'first_name' => 'Ali',
            'last_name' => 'Test',
            'phone' => '09120000000',
        ]);

        $this->deleteJson('/api/profile/advertise-form/'.$form->id)->assertUnauthorized();
    }

    public function test_advertise_form_destroy_forbidden_without_permission(): void
    {
        $this->actingAsAdmin();
        $form = AdvertiseForm::create([
            'first_name' => 'Ali',
            'last_name' => 'Test',
            'phone' => '09120000000',
        ]);

        $this->actingAsUser();

        $this->deleteJson('/api/profile/advertise-form/'.$form->id)->assertForbidden();
    }

    public function test_advertise_form_destroy_deletes_record(): void
    {
        $this->actingAsAdmin();
        $form = AdvertiseForm::create([
            'first_name' => 'Ali',
            'last_name' => 'Test',
            'phone' => '09120000000',
        ]);

        $this->deleteJson('/api/profile/advertise-form/'.$form->id)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseMissing('advertise_forms', ['id' => $form->id]);
    }
}
