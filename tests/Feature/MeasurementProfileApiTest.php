<?php

namespace Tests\Feature;

use App\Models\MeasurementDefinition;
use App\Models\User;
use App\Services\Measurements\MeasurementProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MeasurementProfileApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_mobile_can_fetch_localized_measurement_definitions_in_inches(): void
    {
        $this->getJson('/api/v1/en/measurements/definitions?garment_type=perahan_tunban&unit=in')
            ->assertOk()
            ->assertJsonPath('data.0.unit', 'in')
            ->assertJsonStructure([
                'data' => [[
                    'uuid', 'code', 'name', 'instructions', 'guide_image',
                    'required', 'min', 'max', 'step', 'unit',
                ]],
            ]);
    }

    public function test_customer_can_create_reusable_measurement_profile_and_values_store_in_cm(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = $this->profilePayload('perahan_tunban', 'cm');

        $response = $this->postJson('/api/v1/en/measurement-profiles', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'My Perahan')
            ->assertJsonPath('data.garment_type', 'perahan_tunban')
            ->assertJsonPath('data.is_default', true);

        $profile = $user->measurementProfiles()->with('values.definition')->firstOrFail();
        $this->assertSame(
            number_format((float) $payload['measurements'][0]['value'], 2, '.', ''),
            $profile->values->first()->value_cm,
        );
    }

    public function test_profile_rejects_out_of_range_and_unknown_measurements(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = $this->profilePayload('waistcoat', 'cm');
        $payload['measurements'][0]['value'] = 999;
        $payload['measurements'][] = ['code' => 'not-a-real-measurement', 'value' => 80];

        $this->postJson('/api/v1/en/measurement-profiles', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'measurements.shoulder',
                'measurements.not-a-real-measurement',
            ]);
    }

    public function test_customer_cannot_update_another_customers_profile(): void
    {
        $owner = User::factory()->create();
        $profile = app(MeasurementProfileService::class)->save(
            $owner,
            $this->profilePayload('dress', 'cm'),
        );

        Sanctum::actingAs(User::factory()->create());

        $this->putJson('/api/v1/en/measurement-profiles/'.$profile->uuid, $this->profilePayload('dress', 'cm'))
            ->assertNotFound();
    }

    public function test_measurement_pages_are_private_noindex_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/en/measurements')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex,nofollow">', false)
            ->assertSee('Measurement Profiles');
    }

    private function profilePayload(string $garmentType, string $unit): array
    {
        $definitions = MeasurementDefinition::query()
            ->where('garment_type', $garmentType)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return [
            'name' => $garmentType === 'perahan_tunban' ? 'My Perahan' : 'My Fit',
            'garment_type' => $garmentType,
            'display_unit' => $unit,
            'measurements' => $definitions->map(fn ($definition) => [
                'code' => $definition->code,
                'value' => ((float) $definition->min_cm + (float) $definition->max_cm) / 2,
            ])->all(),
        ];
    }
}
