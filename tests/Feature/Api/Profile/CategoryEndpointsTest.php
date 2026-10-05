<?php

namespace Tests\Feature\Api\Profile;

use App\Models\Category;

class CategoryEndpointsTest extends ProfileTestCase
{
    public function test_category_index_requires_auth(): void
    {
        $this->getJson('/api/profile/categories')->assertUnauthorized();
    }

    public function test_category_index_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile/categories')->assertForbidden();
    }

    public function test_category_index_returns_categories(): void
    {
        $admin = $this->actingAsAdmin();

        Category::factory()->create([
            'title' => 'Football',
            'user_id' => $admin->id,
            'status' => 1,
            'menu' => 1,
        ]);

        $this->getJson('/api/profile/categories')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Football']);
    }
}
