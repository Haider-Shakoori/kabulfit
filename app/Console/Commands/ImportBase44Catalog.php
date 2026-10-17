<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\LegacyProductRedirect;
use App\Models\Product;
use App\Models\ProductTranslation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportBase44Catalog extends Command
{
    protected $signature = 'kabulfit:import-base44
        {snapshot : Local JSON export of the published Base44 Product and Category records}
        {--expected-total=141 : Expected complete Product record count}
        {--expected-active=130 : Expected active Product record count}
        {--commit : Apply the import after completing a dry-run}';

    protected $description = 'Import the original Base44 catalog and exact USD prices, using locally archived images';

    public function handle(): int
    {
        $path = (string) $this->argument('snapshot');

        if (! is_file($path) || ! is_readable($path)) {
            $this->error('Provide a readable local catalog snapshot, not a remote URL.');

            return self::FAILURE;
        }

        try {
            $source = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            $this->error('Invalid Base44 catalog snapshot: '.$exception->getMessage());

            return self::FAILURE;
        }

        $products = $source['products'] ?? null;
        $categories = $source['categories'] ?? null;

        if (! is_array($products) || ! is_array($categories)) {
            $this->error('Snapshot must include products and categories arrays.');

            return self::FAILURE;
        }

        $active = count(array_filter($products, fn (array $product): bool => (bool) ($product['is_active'] ?? false)));

        if (count($products) !== (int) $this->option('expected-total') ||
            $active !== (int) $this->option('expected-active')) {
            $this->error('Export is incomplete or changed: got '.count($products).' products, '.$active.' active.');

            return self::FAILURE;
        }

        $mediaPaths = [];
        $missingInactive = 0;
        $productIds = [];

        foreach ($products as $product) {
            $id = (string) ($product['id'] ?? '');

            if ($id === '' || isset($productIds[$id])) {
                $this->error('Invalid or duplicate Base44 product ID.');

                return self::FAILURE;
            }

            $productIds[$id] = true;

            if ((bool) ($product['is_active'] ?? false) && (float) ($product['price'] ?? 0) <= 0) {
                $this->error('Active product lacks a positive original USD price: '.$id);

                return self::FAILURE;
            }

            foreach ($product['images'] ?? [] as $url) {
                $url = (string) $url;

                if (! isset($mediaPaths[$url])) {
                    try {
                        $mediaPaths[$url] = $this->localImage($url);
                    } catch (\InvalidArgumentException $exception) {
                        $this->error($exception->getMessage());

                        return self::FAILURE;
                    }
                }

                if (! is_file(public_path($mediaPaths[$url]))) {
                    if ((bool) ($product['is_active'] ?? false)) {
                        $this->error('Missing original image for active product '.$id.': '.$url);

                        return self::FAILURE;
                    }

                    $missingInactive++;
                }
            }
        }

        $this->info('Original Base44 export: '.count($products).' products, '.$active.' active, '.
            count($categories).' categories, '.count($mediaPaths).' image references.');
        $this->warn($missingInactive.' unavailable image references belong to inactive records.');

        if (! $this->option('commit')) {
            $this->comment('Read-only validation passed. Supply --commit to import into the selected database.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($categories, $products, $mediaPaths): void {
            $categoryMap = [];
            $ordered = collect($categories)->sortBy(fn (array $row): int => empty($row['parent_category']) ? 0 : 1);

            foreach ($ordered as $offset => $row) {
                $slug = (string) $row['slug'];
                $category = Category::query()
                    ->whereHas('translations', fn ($query) => $query->where('locale', 'en')->where('slug', $slug))
                    ->first();

                if (! $category) {
                    $category = Category::query()->create([
                        'sort_order' => 100 + ((int) $offset * 10),
                        'is_active' => (bool) ($row['is_active'] ?? true),
                    ]);
                }

                foreach (['en' => 'name', 'fa' => 'name_dari', 'ps' => 'name_pashto'] as $locale => $field) {
                    $name = trim((string) ($row[$field] ?: $row['name']));
                    $category->translations()->updateOrCreate(
                        ['locale' => $locale],
                        ['name' => $name, 'slug' => $slug, 'description' => $row['description'] ?? null],
                    );
                }

                $categoryMap[(string) $row['id']] = $category;
            }

            foreach ($categories as $row) {
                $category = $categoryMap[(string) $row['id']];
                $parent = (string) ($row['parent_category'] ?? '');
                $category->update(['parent_id' => $categoryMap[$parent]->id ?? null]);
            }

            $unmapped = null;
            foreach ($products as $index => $row) {
                $id = (string) $row['id'];
                $categoryId = (string) ($row['subcategory'] ?: $row['category_id']);
                $category = $categoryMap[$categoryId]
                    ?? $categoryMap[(string) ($row['category_id'] ?? '')]
                    ?? null;

                if (! $category) {
                    $unmapped ??= Category::query()->create([
                        'is_active' => false, 'sort_order' => 9999,
                    ]);
                    foreach (['en', 'fa', 'ps'] as $locale) {
                        $unmapped->translations()->updateOrCreate(
                            ['locale' => $locale],
                            ['name' => 'Base44 archived category', 'slug' => 'base44-unmapped-archive'],
                        );
                    }
                    $category = $unmapped;
                }

                $old = LegacyProductRedirect::query()->where('legacy_key', $id)->first()?->product;
                $product = $old ?? Product::query()->firstOrNew(['sku' => 'B44-'.strtoupper($id)]);
                $product->fill([
                    'category_id' => $category->id,
                    'price_minor' => (int) round(((float) $row['price']) * 100),
                    'sale_price_minor' => isset($row['sale_price']) ? (int) round(((float) $row['sale_price']) * 100) : null,
                    'currency' => 'USD',
                    'stock_quantity' => (int) ($row['stock_quantity'] ?? 0),
                    'size_options' => array_values(array_unique(array_filter(array_map(
                        fn ($size): string => strtoupper(trim((string) $size)),
                        (array) ($row['sizes'] ?? []),
                    ), fn (string $size): bool => preg_match('/^[A-Z0-9-]{1,20}$/', $size) === 1))),
                    'is_active' => (bool) ($row['is_active'] ?? false),
                    'is_featured' => (bool) ($row['is_featured'] ?? false),
                    'sort_order' => ($index + 1) * 10,
                    'tailoring_enabled' => (bool) ($row['allows_custom_size'] ?? false),
                    'measurement_garment_type' => in_array((string) ($row['category_id'] ?? ''), [
                        '69477bc8405f92df484cb237', '69702e8e1327f73d27b9ecaa',
                    ], true) ? 'dress' : 'perahan_tunban',
                ]);
                $product->save();

                foreach (['en' => ['', ''], 'fa' => ['_dari', '_dari'], 'ps' => ['_pashto', '_pashto']] as $locale => [$nameSuffix, $descSuffix]) {
                    $name = trim((string) ($row['name'.$nameSuffix] ?: $row['name']));
                    $description = trim((string) ($row['description'.$descSuffix] ?: $row['description'] ?? ''));
                    $slug = Str::slug(trim((string) ($row['slug'] ?: $row['name']))) ?: 'base44-product-'.$id;

                    if (ProductTranslation::query()
                        ->where('locale', $locale)
                        ->where('slug', $slug)
                        ->where('product_id', '!=', $product->id)
                        ->exists()) {
                        $slug .= '-'.$id;
                    }

                    $product->translations()->updateOrCreate(['locale' => $locale], [
                        'name' => $name,
                        'slug' => $slug,
                        'short_description' => Str::limit($description, 240),
                        'description' => $description,
                        'seo_title' => Str::limit($name.' | KabulFit', 255),
                        'seo_description' => Str::limit($description, 300),
                    ]);
                }

                $product->media()->delete();
                foreach ($row['images'] ?? [] as $i => $url) {
                    $relative = $mediaPaths[(string) $url];
                    $fullPath = public_path($relative);
                    if (! is_file($fullPath)) {
                        continue; // Only unavailable Base44 images of inactive products.
                    }
                    $image = getimagesize($fullPath);
                    if (! $image) {
                        throw new \RuntimeException('Downloaded image could not be decoded: '.$relative);
                    }

                    $media = $product->media()->create([
                        'path' => $relative,
                        'mime_type' => image_type_to_mime_type($image[2]),
                        'width' => (int) $image[0],
                        'height' => (int) $image[1],
                        'sort_order' => ($i + 1) * 10,
                        'is_primary' => $i === 0,
                    ]);

                    foreach (['en' => '', 'fa' => '_dari', 'ps' => '_pashto'] as $locale => $suffix) {
                        $media->translations()->create([
                            'locale' => $locale,
                            'alt_text' => trim((string) ($row['name'.$suffix] ?: $row['name'])),
                        ]);
                    }
                }

                LegacyProductRedirect::query()->updateOrCreate(
                    ['legacy_key' => $id], ['product_id' => $product->id],
                );
            }

            // These five deterministic Laravel fixtures were never Base44 products.
            // Hide them from the migrated public store without deleting records.
            Product::query()->whereIn('sku', [
                'KF-M-PT-001', 'KF-W-DR-001', 'KF-K-VS-001', 'KF-M-WC-001', 'KF-A-KS-001',
            ])->update(['is_active' => false, 'is_featured' => false]);
        });

        $this->info('Imported '.count($products).' original Base44 products with their exact USD prices.');

        return self::SUCCESS;
    }

    private function localImage(string $url): string
    {
        $hostname = parse_url($url, PHP_URL_HOST);
        $extension = strtolower(pathinfo((string) parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

        if (! in_array($hostname, ['base44.app', 'qtrypzzcjebvfcihiynt.supabase.co'], true) ||
            ! in_array($extension, ['webp', 'jpg', 'jpeg', 'png'], true) ||
            parse_url($url, PHP_URL_SCHEME) !== 'https') {
            throw new \InvalidArgumentException('Unapproved original image reference: '.$url);
        }

        return 'images/kabulfit-base44/catalog/'.substr(hash('sha256', $url), 0, 20).'.'.$extension;
    }
}
