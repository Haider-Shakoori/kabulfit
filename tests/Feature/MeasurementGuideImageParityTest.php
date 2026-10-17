<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeasurementGuideImageParityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_measurement_guide_restores_original_base44_step_artwork(): void
    {
        $html = $this->get('/en/measurement-guide')->assertOk()->getContent();

        $this->assertStringContainsString('data-measurement-guide="interactive"', $html);

        $images = [
            '8a81aa932_neckmeasure.png',
            '12e22a9d0_shuldermeasure.png',
            'cfa4c252f_UnderArm.png',
            'bac309e26_2.png',
            '6eca0d92f_SleeveLength.png',
            'b54401e30_Bicep.png',
            '63998adda_wrist.png',
            'e9d011494_DressLength1.png',
            '47f29dd78_DressHemWidth.png',
            '83ddd063b_TrouserLength.png',
            '87a024cde_LegOpening.png',
            'e2ac85db0_Neck.png',
            'b5e2b7848_Shoulder.png',
            '5f0b6db2c_Underarm.png',
            '0040f6f9d_Bust.png',
            '750a713eb_Waist.png',
            'd2aabc8ae_Hips.png',
            '478755a70_SleeveLength.png',
            '2183cd7d2_Armhole1.png',
            '316edb797_Bicep.png',
            'b56615645_Wrist.png',
            '01fee21b7_DressFullLength.png',
            'ca7c52ac3_TrouserLength.png',
            '53b508b4f_DressTopLength.png',
            '177acd4f8_SkirtLength.png',
            '47616e12d_BustToBust.png',
            'd99476581_FrontNeckDrop.png',
            '63492a53b_BackNeckDrop.png',
        ];

        foreach ($images as $image) {
            $this->assertStringContainsString($image, $html);
            $this->assertFileExists(public_path('images/kabulfit-base44/source/'.$image));
        }

        $this->assertStringContainsString('Male', $html);
        $this->assertStringContainsString('Female', $html);
        $this->assertStringContainsString('Watch Video', $html);
        $this->assertStringNotContainsString('grid gap-6 md:grid-cols-2 xl:grid-cols-3', $html);
    }

    public function test_measurement_guide_enter_tab_matches_base44_inline_form_structure(): void
    {
        $html = $this->get('/en/measurement-guide')->assertOk()->getContent();

        $this->assertStringContainsString('role="tablist"', $html);
        $this->assertStringContainsString('How to Measure', $html);
        $this->assertStringContainsString('Enter Measurements', $html);
        $this->assertStringContainsString('data-measurement-entry-form', $html);
        $this->assertStringContainsString('Enter Your Measurements', $html);
        $this->assertStringContainsString('Body Type', $html);
        $this->assertStringContainsString('Weight', $html);
        $this->assertStringContainsString('Measurement Unit', $html);
        $this->assertStringContainsString('Save as Profile', $html);
        $this->assertStringContainsString('max-w-md grid-cols-2 rounded-lg bg-gray-100 p-1', $html);
        $this->assertStringNotContainsString('mx-auto max-w-3xl rounded-2xl border border-gray-200 bg-white p-6 text-center', $html);
    }

    public function test_signed_in_customer_sees_saveable_inline_measurement_form(): void
    {
        $user = User::factory()->create();

        $html = $this->actingAs($user)
            ->get('/en/measurement-guide')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('action="'.route('measurements.store', ['locale' => 'en']).'"', $html);
        $this->assertStringContainsString('name="garment_type"', $html);
        $this->assertStringContainsString('name="display_unit"', $html);
        $this->assertStringContainsString('name="name"', $html);
        $this->assertStringContainsString('name="measurements[0][code]"', $html);
        $this->assertStringContainsString('name="measurements[0][value]"', $html);
        $this->assertStringContainsString('Save Measurements', $html);
    }

    public function test_homepage_static_content_uses_original_base44_images(): void
    {
        $html = $this->get('/en')->assertOk()->getContent();

        foreach ([
            '6b7f27f2f_H1.webp',
            'f2989fdbd_H2.webp',
            'b6b455e59_H3.webp',
            '2b37f3480_1.webp',
            '0d9b271e2_1.webp',
            '9a8998f13_5.png',
            'b31ec3a1c_1.webp',
            '1cce53a01_2.png',
            'f065351e6_bn.jpg',
            '58f1df170_2.png',
        ] as $image) {
            $this->assertStringContainsString($image, $html);
        }

        $this->assertStringNotContainsString('hero-h1-mobile.webp', $html);
        $this->assertStringNotContainsString('kabulfit-optimized/story-bg.webp', $html);
        $this->assertStringNotContainsString('kabulfit-optimized/craftsmanship.webp', $html);
        $this->assertStringNotContainsString('kabulfit-optimized/measurement.webp', $html);
    }
}
