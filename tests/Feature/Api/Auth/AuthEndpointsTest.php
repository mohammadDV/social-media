<?php

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use App\Services\TelegramNotificationService;
use Google_Client;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class AuthEndpointsTest extends TestCase
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

    protected function mockGoogleClient(array|false|null $payload): void
    {
        $client = Mockery::mock(Google_Client::class);
        $client->shouldReceive('verifyIdToken')->andReturn($payload === null ? false : $payload);
        $this->app->instance(Google_Client::class, $client);
    }

    public function test_register_validation_fails_with_empty_payload(): void
    {
        $this->postJson('/api/register', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'privacy_policy',
                'mobile',
                'nickname',
                'email',
                'password',
            ]);
    }

    public function test_register_success_creates_user_and_returns_token(): void
    {
        $payload = [
            'first_name' => 'Ali',
            'last_name' => 'Rezaei',
            'privacy_policy' => true,
            'mobile' => '09123456789',
            'nickname' => 'ali_rezaei_test',
            'email' => 'ali.rezaei@example.com',
            'password' => 'Password1!',
            'token' => 'test-recaptcha-token',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertCreated()
            ->assertJsonPath('status', 1)
            ->assertJsonStructure(['token', 'status']);

        $this->assertNotEmpty($response->json('token'));

        $this->assertDatabaseHas('users', [
            'email' => 'ali.rezaei@example.com',
            'nickname' => 'ali_rezaei_test',
            'mobile' => '09123456789',
            'role_id' => 4,
            'status' => 1,
        ]);
    }

    public function test_login_fails_with_bad_credentials(): void
    {
        User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('Password1!'),
            'status' => 1,
            'role_id' => 4,
        ]);

        $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'WrongPass1!',
            'token' => 'test-recaptcha-token',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('status', 0);
    }

    public function test_login_success_returns_token(): void
    {
        User::factory()->create([
            'email' => 'login-ok@example.com',
            'password' => bcrypt('Password1!'),
            'status' => 1,
            'role_id' => 4,
        ]);

        $this->postJson('/api/login', [
            'email' => 'login-ok@example.com',
            'password' => 'Password1!',
            'token' => 'test-recaptcha-token',
        ])
            ->assertOk()
            ->assertJsonPath('status', 1)
            ->assertJsonStructure(['token', 'status']);
    }

    public function test_google_verify_logs_in_existing_user(): void
    {
        $user = User::factory()->create([
            'email' => 'google.existing@example.com',
            'status' => 1,
            'role_id' => 4,
            'password' => bcrypt('Password1!'),
        ]);

        $this->mockGoogleClient([
            'email' => $user->email,
            'name' => 'Google User',
            'given_name' => 'Google',
            'family_name' => 'User',
            'sub' => 'google-sub-existing',
            'picture' => 'https://example.com/photo.jpg',
        ]);

        $this->postJson('/api/google/verify', [
            'token' => 'fake-google-id-token',
        ])
            ->assertAccepted()
            ->assertJsonPath('status', 1)
            ->assertJsonStructure(['token', 'status']);

        $this->assertEquals(1, User::where('email', $user->email)->count());
    }

    public function test_google_verify_creates_new_user(): void
    {
        $this->mockGoogleClient([
            'email' => 'google.new@example.com',
            'name' => 'New Google',
            'given_name' => 'New',
            'family_name' => 'Google',
            'sub' => 'google-sub-new',
            'picture' => 'https://example.com/new.jpg',
        ]);

        $this->postJson('/api/google/verify', [
            'token' => 'fake-google-id-token-new',
        ])
            ->assertAccepted()
            ->assertJsonPath('status', 1)
            ->assertJsonStructure(['token', 'status']);

        $this->assertDatabaseHas('users', [
            'email' => 'google.new@example.com',
            'google_id' => 'google-sub-new',
            'first_name' => 'New',
            'last_name' => 'Google',
            'role_id' => 4,
            'status' => 1,
        ]);
    }

    public function test_google_verify_fails_with_invalid_token(): void
    {
        $this->mockGoogleClient(false);

        $this->postJson('/api/google/verify', [
            'token' => 'invalid-token',
        ])
            ->assertBadRequest()
            ->assertJsonPath('status', 0)
            ->assertJsonPath('token', '');
    }

    public function test_logout_requires_authentication(): void
    {
        $this->postJson('/api/logout')
            ->assertUnauthorized();
    }

    public function test_logout_deletes_tokens(): void
    {
        $user = User::factory()->create([
            'status' => 1,
            'role_id' => 4,
            'password' => bcrypt('Password1!'),
        ]);
        $user->assignRole('user');

        $token = $user->createToken('myapptokens');

        $this->withToken($token->plainTextToken)
            ->postJson('/api/logout')
            ->assertCreated()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
        ]);
    }
}
