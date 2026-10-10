<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\LegacyProductRedirect;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Base44HomepageCatalogSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $categories = $this->seedCategories();
            $this->seedFeaturedProducts($categories);

            Product::query()
                ->whereIn('sku', ['KF-M-PT-001', 'KF-W-DR-001', 'KF-K-VS-001', 'KF-M-WC-001', 'KF-A-KS-001'])
                ->update(['is_featured' => false]);

            Product::query()
                ->where('sku', 'KF-A-KS-001')
                ->update([
                    'category_id' => $categories['women']->id,
                    'is_active' => false,
                ]);
        });
    }

    private function seedCategories(): array
    {
        $rows = json_decode(
            (string) file_get_contents(database_path('data/base44_home_categories.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        $categories = [];

        foreach ($rows as $row) {
            $sortOrder = ((int) $row['display_order']) * 10;
            $category = Category::query()->updateOrCreate(
                ['sort_order' => $sortOrder],
                [
                    'parent_id' => null,
                    'image_url' => 'images/kabulfit-base44/source/'.$row['image_filename'],
                    'is_active' => true,
                ],
            );

            $localizedNames = [
                'en' => trim((string) $row['name']),
                'fa' => trim((string) ($row['name_dari'] ?: $row['name'])),
                'ps' => trim((string) ($row['name_pashto'] ?: $row['name'])),
            ];

            foreach ($localizedNames as $locale => $name) {
                $category->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'name' => $name,
                        'slug' => $row['slug'],
                        'description' => filled($row['description'] ?? null) ? trim((string) $row['description']) : null,
                        'seo_title' => $name.' | KabulFit',
                        'seo_description' => filled($row['description'] ?? null) ? trim((string) $row['description']) : null,
                    ],
                );
            }

            $categories[$row['slug']] = $category;
        }

        return $categories;
    }

    private function seedFeaturedProducts(array $categories): void
    {
        $rows = json_decode(
            (string) file_get_contents(database_path('data/base44_home_featured_products.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        $base44CategoryMap = [
            '69477bc8405f92df484cb236' => 'men',
            '69477bc8405f92df484cb237' => 'women',
            '69477bc8405f92df484cb238' => 'boys',
            '69702e8e1327f73d27b9ecaa' => 'girls',
        ];

        foreach ($rows as $row) {
            $categorySlug = $base44CategoryMap[$row['base44_category_id']] ?? null;
            if (! $categorySlug || ! isset($categories[$categorySlug])) {
                continue;
            }

            $slug = filled($row['slug'] ?? null)
                ? trim((string) $row['slug'])
                : Str::slug((string) $row['name']);

            $product = Product::query()->updateOrCreate(
                ['sku' => 'B44-'.strtoupper(substr((string) $row['base44_id'], 0, 12))],
                [
                    'category_id' => $categories[$categorySlug]->id,
                    'price_minor' => (int) round(((float) $row['price']) * 100),
                    'sale_price_minor' => filled($row['sale_price'] ?? null)
                        ? (int) round(((float) $row['sale_price']) * 100)
                        : null,
                    'currency' => 'USD',
                    'stock_quantity' => (int) ($row['stock_quantity'] ?? 100),
                    'is_active' => true,
                    'is_featured' => true,
                    'sort_order' => ((int) $row['display_order']) * 10,
                    'tailoring_enabled' => (bool) ($row['allows_custom_size'] ?? false),
                    'measurement_garment_type' => in_array($categorySlug, ['women', 'girls'], true) ? 'dress' : 'perahan_tunban',
                ],
            );

            $translations = [
                'en' => [
                    'name' => trim((string) $row['name']),
                    'description' => (string) ($row['description'] ?? ''),
                ],
                'fa' => [
                    'name' => trim((string) ($row['name_dari'] ?: $row['name'])),
                    'description' => (string) ($row['description_dari'] ?: $row['description'] ?? ''),
                ],
                'ps' => [
                    'name' => trim((string) ($row['name_pashto'] ?: $row['name'])),
                    'description' => (string) ($row['description_pashto'] ?: $row['description'] ?? ''),
                ],
            ];

            foreach ($translations as $locale => $translation) {
                $product->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'name' => $translation['name'],
                        'slug' => $slug,
                        'short_description' => Str::limit(trim($translation['description']), 240),
                        'description' => trim($translation['description']),
                        'seo_title' => $translation['name'].' | KabulFit',
                        'seo_description' => Str::limit(trim($translation['description']), 300),
                    ],
                );
            }

            $product->media()->delete();
            $media = $product->media()->create([
                'path' => 'images/kabulfit-base44/source/'.$row['primary_image_filename'],
                'mime_type' => 'image/webp',
                'width' => 1200,
                'height' => 1600,
                'sort_order' => 10,
                'is_primary' => true,
            ]);

            foreach ($translations as $locale => $translation) {
                $media->translations()->create([
                    'locale' => $locale,
                    'alt_text' => $translation['name'].' — KabulFit',
                ]);
            }

            LegacyProductRedirect::query()->updateOrCreate(
                ['legacy_key' => $row['base44_id']],
                ['product_id' => $product->id],
            );
        }
    }
}
