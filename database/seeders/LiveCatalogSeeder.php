<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LiveCatalogSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $categories = Category::query()
                ->whereIn('sort_order', [10, 20, 30, 40])
                ->get()
                ->keyBy('sort_order');

            $definitions = [
            [
                'sku' => 'LIVE-F-01',
                'name' => '2-Piece Afghan Traditional Boys Set (Kameez & Tunban)',
                'price_minor' => 5900,
                'category_sort' => 30,
                'sort_order' => 10,
                'primary' => 'images/kabulfit-live/exact/featured-01-1.webp',
                'secondary' => 'images/kabulfit-live/exact/featured-01-2.webp',
            ],
            [
                'sku' => 'LIVE-F-02',
                'name' => '3-Piece Afghan Traditional Embroidered Boys Set (Kameez, Tunban & Shawl)',
                'price_minor' => 9900,
                'category_sort' => 30,
                'sort_order' => 20,
                'primary' => 'images/kabulfit-live/exact/featured-02-1.webp',
                'secondary' => 'images/kabulfit-live/exact/featured-02-2.webp',
            ],
            [
                'sku' => 'LIVE-F-03',
                'name' => 'Midnight Sapphire Heritage Embroidery – Afghan Luxury Floor-Length Ensemble',
                'price_minor' => 24900,
                'category_sort' => 20,
                'sort_order' => 30,
                'primary' => 'images/kabulfit-live/exact/featured-03-1.webp',
                'secondary' => 'images/kabulfit-live/exact/featured-03-2.webp',
            ],
            [
                'sku' => 'LIVE-F-04',
                'name' => 'Amber Royale – Afghan Red & Black Embroidered Floor-Length Dress',
                'price_minor' => 24900,
                'category_sort' => 20,
                'sort_order' => 40,
                'primary' => 'images/kabulfit-live/exact/featured-04-1.webp',
                'secondary' => 'images/kabulfit-live/exact/featured-04-2.webp',
            ],
            [
                'sku' => 'LIVE-F-05',
                'name' => 'Roya Signature Embroidery – Afghan Kids Dress Set',
                'price_minor' => 9900,
                'category_sort' => 40,
                'sort_order' => 50,
                'primary' => 'images/kabulfit-live/exact/featured-05-1.webp',
                'secondary' => 'images/kabulfit-live/exact/featured-05-2.webp',
            ],
            [
                'sku' => 'LIVE-F-06',
                'name' => 'Luxury Floral Embroidery – Afghan Kids Dress',
                'price_minor' => 9900,
                'category_sort' => 40,
                'sort_order' => 60,
                'primary' => 'images/kabulfit-live/exact/featured-06-1.webp',
                'secondary' => 'images/kabulfit-live/exact/featured-06-2.webp',
            ],
            [
                'sku' => 'LIVE-F-07',
                'name' => '3-Piece Imperial Embroidered Set (Kameez, Tunban & Shawl) — Royal Onyx',
                'price_minor' => 14900,
                'category_sort' => 10,
                'sort_order' => 70,
                'primary' => 'images/kabulfit-live/exact/featured-07-1.webp',
                'secondary' => 'images/kabulfit-live/exact/featured-07-2.webp',
            ],
            [
                'sku' => 'LIVE-F-08',
                'name' => 'Snow White Royal Three-Piece Afghan Ensemble with Emerald Embroidery',
                'price_minor' => 12900,
                'category_sort' => 10,
                'sort_order' => 80,
                'primary' => 'images/kabulfit-live/exact/featured-08-1.webp',
                'secondary' => 'images/kabulfit-live/exact/featured-08-2.webp',
            ],
            [
                'sku' => 'LIVE-B-01',
                'name' => '3-Piece Elite Embroidered Set (Kameez, Tunban & Shawl) — Royal Cobalt',
                'price_minor' => 12900,
                'category_sort' => 10,
                'sort_order' => 100,
                'primary' => 'images/kabulfit-live/exact/best-01-1.webp',
                'secondary' => 'images/kabulfit-live/exact/best-01-2.webp',
            ],
            [
                'sku' => 'LIVE-B-02',
                'name' => '3-Piece Ceremonial Embroidered Set (Kameez, Tunban & Shawl) — Ivory Midnight',
                'price_minor' => 12900,
                'category_sort' => 10,
                'sort_order' => 110,
                'primary' => 'images/kabulfit-live/exact/best-02-1.webp',
                'secondary' => 'images/kabulfit-live/exact/best-02-2.webp',
            ],
            [
                'sku' => 'LIVE-B-03',
                'name' => '3-Piece Royal Artisanal Set (Kameez, Tunban & Shawl) —Azure',
                'price_minor' => 12500,
                'category_sort' => 10,
                'sort_order' => 120,
                'primary' => 'images/kabulfit-live/exact/best-03-1.webp',
                'secondary' => 'images/kabulfit-live/exact/best-03-2.webp',
            ],
            [
                'sku' => 'LIVE-B-04',
                'name' => '3-Piece Prestige Embroidered Set (Kameez, Tunban & Shawl) — Sage Olive',
                'price_minor' => 11900,
                'category_sort' => 10,
                'sort_order' => 130,
                'primary' => 'images/kabulfit-live/exact/best-04-1.webp',
                'secondary' => 'images/kabulfit-live/exact/best-04-2.webp',
            ],
            [
                'sku' => 'LIVE-B-05',
                'name' => 'Plum Olive Afghan Traditional Embroidered Women\'s Set',
                'price_minor' => 19900,
                'category_sort' => 20,
                'sort_order' => 140,
                'primary' => 'images/kabulfit-live/exact/best-05-1.webp',
                'secondary' => 'images/kabulfit-live/exact/best-05-2.webp',
            ],
            [
                'sku' => 'LIVE-B-06',
                'name' => 'White Rainbow Afghan Traditional Embroidered Women\'s Set',
                'price_minor' => 15900,
                'category_sort' => 20,
                'sort_order' => 150,
                'primary' => 'images/kabulfit-live/exact/best-06-1.webp',
                'secondary' => 'images/kabulfit-live/exact/best-06-2.webp',
            ],
            [
                'sku' => 'LIVE-B-07',
                'name' => 'Turquoise Dynasty Grace – Afghan Royal 3-Piece Embroidered Ensemble (Dress, Baggy Trousers & Dupatta)',
                'price_minor' => 17900,
                'category_sort' => 20,
                'sort_order' => 160,
                'primary' => 'images/kabulfit-live/exact/best-07-1.webp',
                'secondary' => 'images/kabulfit-live/exact/best-07-2.webp',
            ],
            [
                'sku' => 'LIVE-B-08',
                'name' => 'Ocean Mist Heritage Embroidery – Afghan Traditional Dress',
                'price_minor' => 14900,
                'category_sort' => 20,
                'sort_order' => 170,
                'primary' => 'images/kabulfit-live/exact/best-08-1.webp',
                'secondary' => 'images/kabulfit-live/exact/best-08-2.webp',
            ],
            [
                'sku' => 'LIVE-B-09',
                'name' => '2-Piece Afghan Traditional Embroidered Boys Set (Kameez & Tunban)',
                'price_minor' => 7900,
                'category_sort' => 30,
                'sort_order' => 180,
                'primary' => 'images/kabulfit-live/exact/best-09-1.webp',
                'secondary' => 'images/kabulfit-live/exact/best-09-2.webp',
            ],
            [
                'sku' => 'LIVE-B-10',
                'name' => '2-Piece Afghan Traditional Embroidered Boys Set (Kameez & Tunban)',
                'price_minor' => 8500,
                'category_sort' => 30,
                'sort_order' => 190,
                'primary' => 'images/kabulfit-live/exact/best-10-1.webp',
                'secondary' => 'images/kabulfit-live/exact/best-10-2.webp',
            ],
            [
                'sku' => 'LIVE-B-11',
                'name' => '2-Piece Afghan Traditional Embroidered Boys Set (Kameez & Tunban)',
                'price_minor' => 8500,
                'category_sort' => 30,
                'sort_order' => 200,
                'primary' => 'images/kabulfit-live/exact/best-11-1.webp',
                'secondary' => 'images/kabulfit-live/exact/best-11-2.webp',
            ],
            [
                'sku' => 'LIVE-B-12',
                'name' => '4-Piece Afghan Traditional Embroidered Boys Set (Kameez, Tunban, Vest & Hat)',
                'price_minor' => 7900,
                'category_sort' => 30,
                'sort_order' => 210,
                'primary' => 'images/kabulfit-live/exact/best-12-1.webp',
                'secondary' => 'images/kabulfit-live/exact/best-12-2.webp',
            ],
            [
                'sku' => 'LIVE-B-13',
                'name' => 'Nile River Elegant Embroidery – Afghan Kids Dress Set',
                'price_minor' => 9900,
                'category_sort' => 40,
                'sort_order' => 220,
                'primary' => 'images/kabulfit-live/exact/best-13-1.webp',
                'secondary' => 'images/kabulfit-live/exact/best-13-2.webp',
            ],
            [
                'sku' => 'LIVE-B-14',
                'name' => 'Pearl Elegant Embroidery – Afghan Kids Dress Set',
                'price_minor' => 8500,
                'category_sort' => 40,
                'sort_order' => 230,
                'primary' => 'images/kabulfit-live/exact/best-14-1.webp',
                'secondary' => 'images/kabulfit-live/exact/best-14-2.webp',
            ],
            [
                'sku' => 'LIVE-B-15',
                'name' => 'Pink Garden Classic Embroidery – Afghan Kids Dress Set',
                'price_minor' => 8500,
                'category_sort' => 40,
                'sort_order' => 240,
                'primary' => 'images/kabulfit-live/exact/best-15-1.webp',
                'secondary' => 'images/kabulfit-live/exact/best-15-2.webp',
            ],
            [
                'sku' => 'LIVE-B-16',
                'name' => 'Cute Fairy Heritage Embroidery – Afghan Kids Dress',
                'price_minor' => 9900,
                'category_sort' => 40,
                'sort_order' => 250,
                'primary' => 'images/kabulfit-live/exact/best-16-1.webp',
                'secondary' => 'images/kabulfit-live/exact/best-16-2.webp',
            ],
            ];

            foreach ($definitions as $definition) {
                $category = $categories->get($definition['category_sort']);

                if (! $category) {
                    continue;
                }

                $isFeatured = str_starts_with($definition['sku'], 'LIVE-F-');

                $product = Product::query()->updateOrCreate(
                    ['sku' => $definition['sku']],
                    [
                        'category_id' => $category->id,
                        'price_minor' => $definition['price_minor'],
                        'sale_price_minor' => null,
                        'currency' => 'USD',
                        'stock_quantity' => 25,
                        'is_active' => true,
                        'is_featured' => $isFeatured,
                        'sort_order' => $definition['sort_order'],
                        'tailoring_enabled' => true,
                        'measurement_garment_type' => $definition['category_sort'] === 10 ? 'perahan_tunban' : 'dress',
                    ],
                );

                foreach (['en', 'fa', 'ps'] as $locale) {
                    $slug = Str::slug($definition['name']).'-'.strtolower($definition['sku']);
                    $description = $definition['name'].' — authentic KabulFit Afghan clothing with made-to-measure tailoring available.';

                    $product->translations()->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'name' => $definition['name'],
                            'slug' => $slug,
                            'short_description' => $description,
                            'description' => $description,
                            'seo_title' => $definition['name'].' | KabulFit',
                            'seo_description' => $description,
                        ],
                    );
                }

                $product->media()->delete();

                foreach ([
                    [$definition['primary'], 10, true],
                    [$definition['secondary'], 20, false],
                ] as [$path, $sortOrder, $isPrimary]) {
                    $media = $product->media()->create([
                        'path' => $path,
                        'mime_type' => 'image/webp',
                        'width' => null,
                        'height' => null,
                        'sort_order' => $sortOrder,
                        'is_primary' => $isPrimary,
                    ]);

                    foreach (['en', 'fa', 'ps'] as $locale) {
                        $media->translations()->create([
                            'locale' => $locale,
                            'alt_text' => $definition['name'],
                        ]);
                    }
                }
            }
        });
    }
}
