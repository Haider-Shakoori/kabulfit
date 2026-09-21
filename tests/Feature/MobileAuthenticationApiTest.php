<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class MobileAuthenticationApiTest extends TestCase
{
    use RefreshDatabase;

    private array $device = [
        'device_uuid' => 'b8b53328-11a3-4ae8-b60d-7f25d299b920',
        'device_name' => 'Haider iPhone',
        'platform' => 'ios',
        'app_version' => '1.0.0',
    ];

    public function test_mobile_registration_returns_device_bound_sanctum_token_without_user_id(): void
    {
        Notification::fake();

        $response = $this->postJson('/api/v1/ps/auth/register', [
            'name' => 'Mobile Customer',
            'email' => 'mobile@example.com',
            'phone' => '+93 700 000 001',
            'password' => 'Secure1234',
            'password_confirmation' => 'Secure1234',
            ...$this->device,
        ])->assertCreated();

        $plainToken = $response->json('data.token');

        $this->assertNotEmpty($plainToken);
        $this->assertSame('Bearer', $response->json('data.token_type'));
        $this->assertSame('ps', $response->json('data.user.preferred_locale'));
        $this->assertArrayNotHasKey('id', $response->json('data.user'));

        $user = User::query()->where('email', 'mobile@example.com')->firstOrFail();
        $this->assertDatabaseHas('user_devices', [
            'user_id' => $user->id,
            'uuid' => $this->device['device_uuid'],
            'platform' => 'ios',
        ]);

        $stored = PersonalAccessToken::query()->firstOrFail();
        $this->assertSame('mobile:'.$this->device['device_uuid'], $stored->name);
        $this->assertNotSame($plainToken, $stored->token);
        $this->assertNotNull($stored->expires_at);
    }

    public function test_mobile_login_account_and_logout_flow(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make('Secure1234'),
        ]);

        $login = $this->postJson('/api/v1/fa/auth/login', [
            'email' => 'LOGIN@example.com',
            'password' => 'Secure1234',
            ...$this->device,
        ])->assertOk();

        $token = $login->json('data.token');

        $this->withToken($token)
            ->getJson('/api/v1/fa/account')
            ->assertOk()
            ->assertJsonPath('data.email', 'login@example.com')
            ->assertJsonPath('data.preferred_locale', 'fa')
            ->assertJsonPath('data.devices.0.uuid', $this->device['device_uuid']);

        $this->withToken($token)
            ->postJson('/api/v1/fa/auth/logout')
            ->assertOk();

        $this->assertCount(0, $user->tokens()->get());
        Auth::forgetGuards();

        $this->withToken($token)
            ->getJson('/api/v1/fa/account')
            ->assertUnauthorized();
    }

    public function test_invalid_or_inactive_mobile_credentials_are_rejected(): void
    {
        User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => Hash::make('Secure1234'),
            'is_active' => false,
        ]);

        $this->postJson('/api/v1/en/auth/login', [
            'email' => 'inactive@example.com',
            'password' => 'Secure1234',
            ...$this->device,
        ])->assertUnprocessable()->assertJsonValidationErrors('email');

        $this->postJson('/api/v1/en/auth/login', [
            'email' => 'inactive@example.com',
            'password' => 'Wrong1234',
            ...$this->device,
        ])->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_reauthenticating_same_device_replaces_previous_token(): void
    {
        $user = User::factory()->create([
            'email' => 'replace@example.com',
            'password' => Hash::make('Secure1234'),
        ]);

        $payload = [
            'email' => 'replace@example.com',
            'password' => 'Secure1234',
            ...$this->device,
        ];

        $first = $this->postJson('/api/v1/en/auth/login', $payload)->assertOk()->json('data.token');
        $second = $this->postJson('/api/v1/en/auth/login', $payload)->assertOk()->json('data.token');

        $this->assertNotSame($first, $second);
        $this->assertCount(1, $user->tokens()->get());

        $this->withToken($first)->getJson('/api/v1/en/account')->assertUnauthorized();
        $this->withToken($second)->getJson('/api/v1/en/account')->assertOk();
    }

    public function test_user_can_revoke_another_owned_mobile_device_but_not_someone_elses(): void
    {
        $user = User::factory()->create(['password' => Hash::make('Secure1234')]);
        $other = User::factory()->create();

        $device = $user->devices()->create([
            'uuid' => fake()->uuid(),
            'name' => 'Android phone',
            'platform' => 'android',
            'last_seen_at' => now(),
        ]);

        $foreign = $other->devices()->create([
            'uuid' => fake()->uuid(),
            'name' => 'Other phone',
            'platform' => 'ios',
            'last_seen_at' => now(),
        ]);

        $token = $user->createToken('mobile:current', ['mobile'])->plainTextToken;

        $this->withToken($token)
            ->deleteJson('/api/v1/en/auth/devices/'.$device->uuid)
            ->assertOk();

        $this->assertNotNull($device->fresh()->revoked_at);

        $this->withToken($token)
            ->deleteJson('/api/v1/en/auth/devices/'.$foreign->uuid)
            ->assertNotFound();
    }
}
