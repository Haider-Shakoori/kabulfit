<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductMediaDerivative;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PerformanceApiHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_public_catalog_emits_cache_headers_and_supports_etag_revalidation(): void
    {
        $response = $this->getJson('/api/v1/en/catalog?per_page=5')->assertOk();

        $etag = $response->headers->get('ETag');
        $cacheControl = (string) $response->headers->get('Cache-Control');

        $this->assertNotNull($etag);
        $this->assertStringContainsString('public', $cacheControl);
        $this->assertStringContainsString('max-age=60', $cacheControl);
        $this->assertStringContainsString('stale-while-revalidate=300', $cacheControl);

        $this->withHeaders(['If-None-Match' => $etag])
            ->getJson('/api/v1/en/catalog?per_page=5')
            ->assertStatus(304);
    }

    public function test_authenticated_api_responses_are_private_and_revalidatable(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/api/v1/en/orders')->assertOk();

        $cacheControl = (string) $response->headers->get('Cache-Control');

        $this->assertStringContainsString('private', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertNotNull($response->headers->get('ETag'));
    }

    public function test_mobile_list_endpoints_enforce_bounded_page_sizes(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/en/orders?per_page=51')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('per_page');

        $this->getJson('/api/v1/en/measurement-profiles?per_page=51')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('per_page');

        $this->getJson('/api/v1/en/tailoring?per_page=51')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('per_page');

        $this->getJson('/api/v1/en/wishlist?per_page=51')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('per_page');
    }

    public function test_product_detail_supports_selective_mobile_expansions(): void
    {
        $product = Product::query()
            ->where('sku', 'KF-M-PT-001')
            ->with('translations')
            ->firstOrFail();
        $slug = $product->translations->firstWhere('locale', 'en')->slug;

        $response = $this->getJson('/api/v1/en/products/'.$slug.'?include=variants')
            ->assertOk()
            ->assertJsonStructure(['data' => ['slug', 'sku', 'name', 'description', 'variants']]);

        $this->assertArrayNotHasKey('media', $response->json('data'));
        $this->assertArrayNotHasKey('collections', $response->json('data'));
        $this->assertArrayNotHasKey('tailoring', $response->json('data'));

        $this->getJson('/api/v1/en/products/'.$slug.'?include=unknown')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('include');
    }

    public function test_catalog_query_count_is_bounded_and_filter_metadata_is_cached(): void
    {
        Cache::setDefaultDriver('array');
        Cache::clear();
        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->getJson('/api/v1/en/catalog?per_page=5')->assertOk();

        $firstCount = count(DB::getQueryLog());

        $this->assertLessThanOrEqual(
            24,
            $firstCount,
            "Catalog query count exceeded the Batch 11 N+1 ceiling: {$firstCount}",
        );

        DB::flushQueryLog();

        $this->getJson('/api/v1/en/catalog?per_page=5')->assertOk();

        $secondCount = count(DB::getQueryLog());

        $this->assertLessThan($firstCount, $secondCount);
    }

    public function test_responsive_media_sources_are_exposed_to_web_and_mobile_clients(): void
    {
        $product = Product::query()
            ->where('sku', 'KF-M-PT-001')
            ->with(['translations', 'primaryMedia'])
            ->firstOrFail();
        $media = $product->primaryMedia;
        $this->assertNotNull($media);

        ProductMediaDerivative::query()->create([
            'product_media_id' => $media->id,
            'disk' => 'public',
            'path' => 'media/products/demo/320.webp',
            'format' => 'webp',
            'width' => 320,
            'height' => 391,
            'byte_size' => 12000,
        ]);
        ProductMediaDerivative::query()->create([
            'product_media_id' => $media->id,
            'disk' => 'public',
            'path' => 'media/products/demo/640.avif',
            'format' => 'avif',
            'width' => 640,
            'height' => 782,
            'byte_size' => 18000,
        ]);

        $slug = $product->translations->firstWhere('locale', 'en')->slug;

        $api = $this->getJson('/api/v1/en/products/'.$slug)
            ->assertOk();

        $this->assertStringContainsString(
            '320w',
            (string) $api->json('data.primary_media.sources.webp'),
        );
        $this->assertStringContainsString(
            '640w',
            (string) $api->json('data.primary_media.sources.avif'),
        );

        $html = $this->get('/en/products/'.$slug)->assertOk()->getContent();

        $this->assertStringContainsString('<picture>', $html);
        $this->assertStringContainsString('type="image/webp"', $html);
        $this->assertStringContainsString('type="image/avif"', $html);
        $this->assertStringContainsString('decoding="async"', $html);
    }

    public function test_responsive_media_generation_command_safely_skips_svg_placeholders(): void
    {
        $this->artisan('media:generate-responsive --sync')
            ->assertExitCode(0);
    }
}
