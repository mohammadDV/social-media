<?php

namespace Tests\Feature\Api;

use App\Services\TelegramNotificationService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RootApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(TelegramNotificationService::class, function ($mock) {
            $mock->shouldReceive('sendNotification')->andReturnNull();
        });

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true], 200),
        ]);
    }

    public function test_sitemap_xml_returns_xml(): void
    {
        $this->get('/api/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false)
            ->assertSee('https://varzeshpod.com', false);
    }

    public function test_validation_endpoint_returns_field_error_for_login_request(): void
    {
        $this->postJson('/api/validation/LoginRequest', [
            'email' => 'not-an-email',
            'password' => 'short',
            'field' => 'email',
        ])
            ->assertOk()
            ->assertJsonPath('status', 1)
            ->assertJsonStructure(['status', 'message']);

        $this->assertNotEmpty($this->postJson('/api/validation/LoginRequest', [
            'email' => 'not-an-email',
            'password' => 'short',
            'field' => 'email',
        ])->json('message'));
    }

    public function test_validation_endpoint_returns_ok_when_field_is_valid(): void
    {
        $this->postJson('/api/validation/LoginRequest', [
            'email' => 'valid@example.com',
            'password' => 'Password1!',
            'token' => 'test-recaptcha-token',
            'field' => 'email',
        ])
            ->assertOk()
            ->assertJson([
                'status' => 0,
                'message' => '',
            ]);
    }

    public function test_user_requires_authentication(): void
    {
        $this->getJson('/api/user')
            ->assertUnauthorized();
    }

    public function test_user_returns_authenticated_user(): void
    {
        $user = $this->actingAsUser([
            'email' => 'me@example.com',
            'nickname' => 'me_user',
        ]);

        $this->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('email', 'me@example.com')
            ->assertJsonPath('nickname', 'me_user');
    }

    public function test_profile_requires_authentication(): void
    {
        $this->getJson('/api/profile')
            ->assertUnauthorized();
    }

    public function test_profile_returns_json_when_authenticated(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile')
            ->assertOk()
            ->assertJson([
                'title' => 'yes',
            ]);
    }
}
