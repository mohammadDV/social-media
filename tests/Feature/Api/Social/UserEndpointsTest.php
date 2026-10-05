<?php

namespace Tests\Feature\Api\Social;

class UserEndpointsTest extends SocialTestCase
{
    public function test_search_users_by_nickname(): void
    {
        $auth = $this->actingAsUser(['nickname' => 'searcher', 'level' => 0]);
        $match = $this->makeUser([
            'nickname' => 'unique-findme',
            'first_name' => 'Find',
            'last_name' => 'Me',
            'level' => 0,
            'status' => 1,
            'is_report' => 0,
        ]);
        $this->makeUser([
            'nickname' => 'other-person',
            'level' => 0,
        ]);

        $response = $this->jsonAs($auth, 'POST', '/api/user/search', [
            'search' => 'unique-findme',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['users', 'clubs']);

        $ids = collect($response->json('users'))->pluck('id');
        $this->assertTrue($ids->contains($match->id));
    }

    public function test_search_with_empty_query_returns_empty_array(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/user/search', [])
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_user_info_returns_authenticated_user_by_default(): void
    {
        $auth = $this->actingAsUser([
            'nickname' => 'info-self',
            'first_name' => 'Self',
        ]);

        $this->getJson('/api/user/info')
            ->assertOk()
            ->assertJsonPath('id', $auth->id)
            ->assertJsonPath('nickname', 'info-self');
    }

    public function test_user_info_returns_specific_user(): void
    {
        $auth = $this->actingAsUser();
        $other = $this->makeUser([
            'nickname' => 'info-other',
            'first_name' => 'Other',
        ]);

        $this->jsonAs($auth, 'GET', "/api/user/info/{$other->id}")
            ->assertOk()
            ->assertJsonPath('id', $other->id)
            ->assertJsonPath('nickname', 'info-other');
    }
}
