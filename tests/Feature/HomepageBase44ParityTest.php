<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class HomepageBase44ParityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Cache::flush();
    }

    public function test_homepage_categories_match_current_base44_order_names_and_media(): void
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        $this->assertSame(
            ['Men', 'Women', 'Boys', 'Girls'],
            $categories->map(fn (Category $category) => $category->translation('en')?->name)->all(),
        );

        $this->assertSame(
            [
                'images/kabulfit-base44/source/2b37f3480_1.webp',
                'images/kabulfit-base44/source/0d9b271e2_1.webp',
                'images/kabulfit-base44/source/9a8998f13_5.png',
                'images/kabulfit-base44/source/b31ec3a1c_1.webp',
            ],
            $categories->pluck('image_url')->all(),
        );

        foreach ($categories as $category) {
            $this->assertFileExists(public_path($category->image_url));
        }
    }

    public function test_homepage_featured_products_match_current_base44_snapshot(): void
    {
        $products = Product::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->with(['translations', 'primaryMedia.translations'])
            ->orderBy('sort_order')
            ->get();

        $this->assertCount(8, $products);

        $this->assertSame([
            '2-Piece Afghan Traditional Boys Set (Kameez & Tunban)',
            '3-Piece Afghan Traditional Embroidered Boys Set (Kameez, Tunban & Shawl)',
            'Midnight Sapphire Heritage Embroidery – Afghan Luxury Floor-Length Ensemble',
            'Amber Royale – Afghan Red & Black Embroidered Floor-Length Dress',
            'Roya Signature Embroidery – Afghan Kids Dress Set',
            'Luxury Floral Embroidery – Afghan Kids Dress',
            '3-Piece Imperial Embroidered Set (Kameez, Tunban & Shawl) — Royal Onyx',
            'Snow White Royal Three-Piece Afghan Ensemble with Emerald Embroidery',
        ], $products->map(fn (Product $product) => $product->translation('en')?->name)->all());

        $this->assertSame(
            ['$59.00', '$99.00', '$249.00', '$249.00', '$99.00', '$99.00', '$149.00', '$129.00'],
            $products->map(fn (Product $product) => $product->formattedPrice())->all(),
        );

        foreach ($products as $product) {
            $this->assertSame('USD', $product->currency);
            $this->assertNotNull($product->primaryMedia);
            $this->assertFileExists(public_path($product->primaryMedia->path));
        }
    }

    public function test_homepage_markup_uses_base44_category_and_featured_content(): void
    {
        $html = $this->get('/en')->assertOk()->getContent();

        foreach ([
            '2b37f3480_1.webp',
            '0d9b271e2_1.webp',
            '9a8998f13_5.png',
            'b31ec3a1c_1.webp',
            '8d8d31b20_15.webp',
            '8c9048b86_15.webp',
            '256587c47_1.webp',
            '47be67ffa_1.webp',
            '839af2bcf_1.webp',
            'ee5ad9db7_1.webp',
            '99067390d_1.webp',
            '16b328506_12.webp',
        ] as $filename) {
            $this->assertStringContainsString($filename, $html);
        }

        $this->assertLessThan(strpos($html, '>Women<'), strpos($html, '>Men<'));
        $this->assertLessThan(strpos($html, '>Boys<'), strpos($html, '>Women<'));
        $this->assertLessThan(strpos($html, '>Girls<'), strpos($html, '>Boys<'));
        $this->assertStringNotContainsString('>Kids<', $html);
        $this->assertStringNotContainsString('>Accessories<', $html);
        $this->assertStringContainsString('base44-gradient-cta', $html);
    }

    public function test_desktop_navigation_uses_base44_gradient_hover_contract(): void
    {
        $html = $this->get('/en')->assertOk()->getContent();
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertGreaterThanOrEqual(5, substr_count($html, 'base44-nav-link'));
        $this->assertGreaterThanOrEqual(4, substr_count($html, 'base44-nav-action'));
        $this->assertStringContainsString('.base44-nav-link:hover', $css);
        $this->assertStringContainsString('linear-gradient(90deg, #881C27, #2A6867)', $css);
        $this->assertStringContainsString('.base44-nav-action:hover', $css);
    }

    public function test_base44_featured_product_ids_keep_legacy_redirects(): void
    {
        $this->get('/ProductDetail?id=6a29f6534f5eac35bc2ee17d')
            ->assertStatus(301)
            ->assertRedirect('/en/products/2-piece-afghan-traditional-boys-set-kameez-tunban');
    }
}
