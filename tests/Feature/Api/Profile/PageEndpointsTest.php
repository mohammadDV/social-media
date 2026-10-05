<?php

namespace Tests\Feature\Api\Profile;

use App\Models\Page;

class PageEndpointsTest extends ProfileTestCase
{
    public function test_page_index_requires_auth(): void
    {
        $this->getJson('/api/profile/pages')->assertUnauthorized();
    }

    public function test_page_index_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile/pages')->assertForbidden();
    }

    public function test_page_index_returns_paginated_list(): void
    {
        $admin = $this->actingAsAdmin();

        Page::create([
            'title' => 'About Us',
            'content' => 'About content',
            'image' => 'https://example.com/about.jpg',
            'status' => 1,
            'priority' => 1,
            'user_id' => $admin->id,
        ]);

        $this->getJson('/api/profile/pages')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.title', 'About Us');
    }

    public function test_page_show_requires_auth(): void
    {
        $page = Page::create([
            'title' => 'Show Page',
            'content' => 'Content',
            'image' => 'https://example.com/s.jpg',
            'status' => 1,
            'priority' => 1,
            'user_id' => 1,
        ]);

        $this->getJson('/api/profile/pages/'.$page->id)->assertUnauthorized();
    }

    public function test_page_show_forbidden_without_permission(): void
    {
        $admin = $this->actingAsAdmin();
        $page = Page::create([
            'title' => 'Show Page',
            'content' => 'Content',
            'image' => 'https://example.com/s.jpg',
            'status' => 1,
            'priority' => 1,
            'user_id' => $admin->id,
        ]);

        $this->actingAsUser();

        $this->getJson('/api/profile/pages/'.$page->id)->assertForbidden();
    }

    public function test_page_show_returns_page(): void
    {
        $admin = $this->actingAsAdmin();
        $page = Page::create([
            'title' => 'Contact',
            'content' => 'Contact content',
            'image' => 'https://example.com/c.jpg',
            'status' => 1,
            'priority' => 2,
            'user_id' => $admin->id,
        ]);

        $this->getJson('/api/profile/pages/'.$page->id)
            ->assertOk()
            ->assertJsonPath('id', $page->id)
            ->assertJsonPath('title', 'Contact');
    }

    public function test_page_store_requires_auth(): void
    {
        $this->postJson('/api/profile/pages', [])->assertUnauthorized();
    }

    public function test_page_store_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/profile/pages', [
            'title' => 'New Page',
            'content' => 'Body',
            'status' => 1,
            'priority' => 1,
            'image' => 'https://example.com/n.jpg',
        ])->assertForbidden();
    }

    public function test_page_store_validation_fails(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/profile/pages', [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_page_store_creates_record(): void
    {
        $admin = $this->actingAsAdmin();

        $this->postJson('/api/profile/pages', [
            'title' => 'Created Page',
            'content' => 'Created content',
            'status' => 1,
            'priority' => 3,
            'image' => 'https://example.com/created-page.jpg',
        ])
            ->assertCreated()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('pages', [
            'title' => 'Created Page',
            'content' => 'Created content',
            'status' => 1,
            'priority' => 3,
            'user_id' => $admin->id,
        ]);
    }

    public function test_page_update_requires_auth(): void
    {
        $page = Page::create([
            'title' => 'Old Page',
            'content' => 'Old',
            'image' => 'https://example.com/o.jpg',
            'status' => 0,
            'priority' => 1,
            'user_id' => 1,
        ]);

        $this->postJson('/api/profile/pages/'.$page->id, [])->assertUnauthorized();
    }

    public function test_page_update_forbidden_without_permission(): void
    {
        $admin = $this->actingAsAdmin();
        $page = Page::create([
            'title' => 'Old Page',
            'content' => 'Old',
            'image' => 'https://example.com/o.jpg',
            'status' => 0,
            'priority' => 1,
            'user_id' => $admin->id,
        ]);

        $this->actingAsUser();

        $this->postJson('/api/profile/pages/'.$page->id, [
            'title' => 'Updated Page',
            'content' => 'Updated',
            'status' => 1,
            'priority' => 2,
            'image' => 'https://example.com/u.jpg',
        ])->assertForbidden();
    }

    public function test_page_update_validation_fails(): void
    {
        $admin = $this->actingAsAdmin();
        $page = Page::create([
            'title' => 'Old Page',
            'content' => 'Old',
            'image' => 'https://example.com/o.jpg',
            'status' => 0,
            'priority' => 1,
            'user_id' => $admin->id,
        ]);

        $this->postJson('/api/profile/pages/'.$page->id, [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_page_update_updates_record(): void
    {
        $admin = $this->actingAsAdmin();
        $page = Page::create([
            'title' => 'Old Page',
            'content' => 'Old',
            'image' => 'https://example.com/o.jpg',
            'status' => 0,
            'priority' => 1,
            'user_id' => $admin->id,
        ]);

        $this->postJson('/api/profile/pages/'.$page->id, [
            'title' => 'Updated Page',
            'content' => 'Updated content',
            'status' => 1,
            'priority' => 9,
            'image' => 'https://example.com/updated-page.jpg',
        ])
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title' => 'Updated Page',
            'content' => 'Updated content',
            'status' => 1,
            'priority' => 9,
        ]);
    }

    public function test_page_destroy_requires_auth(): void
    {
        $page = Page::create([
            'title' => 'Delete Page',
            'content' => 'Bye',
            'image' => 'https://example.com/d.jpg',
            'status' => 1,
            'priority' => 1,
            'user_id' => 1,
        ]);

        $this->deleteJson('/api/profile/pages/'.$page->id)->assertUnauthorized();
    }

    public function test_page_destroy_forbidden_without_permission(): void
    {
        $admin = $this->actingAsAdmin();
        $page = Page::create([
            'title' => 'Delete Page',
            'content' => 'Bye',
            'image' => 'https://example.com/d.jpg',
            'status' => 1,
            'priority' => 1,
            'user_id' => $admin->id,
        ]);

        $this->actingAsUser();

        $this->deleteJson('/api/profile/pages/'.$page->id)->assertForbidden();
    }

    public function test_page_destroy_deletes_record(): void
    {
        $admin = $this->actingAsAdmin();
        $page = Page::create([
            'title' => 'Delete Page',
            'content' => 'Bye',
            'image' => 'https://example.com/d.jpg',
            'status' => 1,
            'priority' => 1,
            'user_id' => $admin->id,
        ]);

        $this->deleteJson('/api/profile/pages/'.$page->id)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }
}
