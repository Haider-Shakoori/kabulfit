<?php

namespace Tests\Feature;

use App\Models\ShippingMethod;
use App\Models\User;
use App\Services\Commerce\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MobileShippingMethodsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_cannot_list_shipping_methods(): void
    {
        $this->getJson('/api/v1/en/shipping-methods')->assertUnauthorized();
    }

    public function test_customer_receives_only_active_shipping_methods_matching_cart_currency(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        ShippingMethod::where('code', 'express-af')->update(['is_active' => false]);
        ShippingMethod::create([
            'code' => 'unsupported-currency',
            'name' => 'Other Currency',
            'currency' => 'EUR',
            'price_minor' => 500,
            'is_active' => true,
            'sort_order' => 5,
        ]);

        $response = $this->getJson('/api/v1/en/shipping-methods')->assertOk();
        $this->assertCount(1, $response->json('data'));
        $response->assertJsonPath('data.0.code', 'standard-af')
            ->assertJsonPath('data.0.currency', config('kabulfit.default_currency'))
            ->assertJsonMissingPath('data.0.id');
    }

    public function test_shipping_options_are_empty_for_carts_in_an_unavailable_currency(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $cart = app(CartService::class)->forUser($user);
        $cart->update(['currency' => 'EUR']);

        $this->getJson('/api/v1/en/shipping-methods')
            ->assertOk()
            ->assertJsonPath('data', []);
    }
}
