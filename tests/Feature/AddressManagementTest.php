<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AddressManagementTest extends TestCase
{
    use RefreshDatabase;

    private array $address = [
        'label' => 'Home',
        'recipient_name' => 'KabulFit Customer',
        'phone' => '+93 700 000 000',
        'country_code' => 'AF',
        'province' => 'Kabul',
        'city' => 'Kabul',
        'address_line1' => 'Street 1, House 10',
        'address_line2' => null,
        'postal_code' => null,
    ];

    public function test_first_web_address_becomes_default_and_default_moves_safely(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/en/account/addresses', $this->address)
            ->assertRedirect();

        $first = $user->addresses()->firstOrFail();
        $this->assertTrue($first->is_default);

        $this->actingAs($user)->post('/en/account/addresses', [
            ...$this->address,
            'label' => 'Work',
            'address_line1' => 'Business District',
            'is_default' => '1',
        ])->assertRedirect();

        $this->assertFalse($first->fresh()->is_default);
        $this->assertTrue($user->addresses()->where('label', 'Work')->firstOrFail()->is_default);
    }

    public function test_customer_cannot_modify_another_customers_address(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();

        $address = $owner->addresses()->create([
            ...$this->address,
            'uuid' => fake()->uuid(),
            'is_default' => true,
        ]);

        $this->actingAs($attacker)
            ->put('/en/account/addresses/'.$address->uuid, [
                ...$this->address,
                'city' => 'Herat',
            ])
            ->assertNotFound();

        $this->assertSame('Kabul', $address->fresh()->city);
    }

    public function test_mobile_address_api_uses_public_uuid_and_enforces_ownership(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['mobile']);

        $response = $this->postJson('/api/v1/en/account/addresses', $this->address)
            ->assertCreated();

        $uuid = $response->json('data.uuid');
        $this->assertNotEmpty($uuid);
        $this->assertArrayNotHasKey('id', $response->json('data'));

        $this->putJson('/api/v1/en/account/addresses/'.$uuid, [
            ...$this->address,
            'city' => 'Mazar-e-Sharif',
        ])->assertOk()->assertJsonPath('data.city', 'Mazar-e-Sharif');

        $this->deleteJson('/api/v1/en/account/addresses/'.$uuid)->assertNoContent();
        $this->assertDatabaseMissing('addresses', ['uuid' => $uuid]);
    }
}
