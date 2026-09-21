<?php

namespace Database\Seeders;

use App\Models\Collection;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogDomainSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $sizes = collect([
                ['XS', 10],
                ['S', 20],
                ['M', 30],
                ['L', 40],
                ['XL', 50],
                ['CUSTOM', 60],
            ])->mapWithKeys(function (array $definition): array {
                [$code, $sortOrder] = $definition;

                $size = Size::query()->updateOrCreate(
                    ['code' => $code],
                    ['sort_order' => $sortOrder, 'is_active' => true],
                );

                return [$code => $size];
            });

            $colors = collect([
                'BLACK' => ['#151515', 10, [
                    'en' => ['Black', 'black'],
                    'fa' => ['مشکی', 'مشکی'],
                    'ps' => ['تور', 'تور'],
                ]],
                'MAROON' => ['#7c2f28', 20, [
                    'en' => ['Royal Maroon', 'royal-maroon'],
                    'fa' => ['زرشکی سلطنتی', 'زرشکی-سلطنتی'],
                    'ps' => ['شاهي سور', 'شاهي-سور'],
                ]],
                'GREEN' => ['#173b2d', 30, [
                    'en' => ['Forest Green', 'forest-green'],
                    'fa' => ['سبز جنگلی', 'سبز-جنگلی'],
                    'ps' => ['ځنګلي شین', 'ځنګلي-شین'],
                ]],
                'CREAM' => ['#eadcc7', 40, [
                    'en' => ['Cream', 'cream'],
                    'fa' => ['کریم', 'کریم'],
                    'ps' => ['کریم', 'کریم'],
                ]],
                'NAVY' => ['#23324d', 50, [
                    'en' => ['Navy', 'navy'],
                    'fa' => ['سرمه‌ای', 'سرمه-ای'],
                    'ps' => ['سمندري آبي', 'سمندري-آبي'],
                ]],
            ])->mapWithKeys(function (array $definition, string $code): array {
                [$hex, $sortOrder, $translations] = $definition;

                $color = Color::query()->updateOrCreate(
                    ['code' => $code],
                    ['hex_value' => $hex, 'sort_order' => $sortOrder, 'is_active' => true],
                );

                foreach ($translations as $locale => [$name, $slug]) {
                    $color->translations()->updateOrCreate(
                        ['locale' => $locale],
                        ['name' => $name, 'slug' => $slug],
                    );
                }

                return [$code => $color];
            });

            $products = Product::query()
                ->with('translations')
                ->whereIn('sku', ['KF-M-PT-001', 'KF-W-DR-001', 'KF-K-VS-001', 'KF-M-WC-001', 'KF-A-KS-001'])
                ->get()
                ->keyBy('sku');

            $this->seedVariants($products, $sizes, $colors);
            $this->seedCollections($products);
            $this->seedMedia($products);
            $this->seedRelatedProducts($products);
        });
    }

    private function seedVariants($products, $sizes, $colors): void
    {
        $definitions = [
            'KF-M-PT-001' => [
                ['M', 'BLACK', 6, 1],
                ['L', 'BLACK', 4, 0],
                ['M', 'GREEN', 3, 0],
                ['CUSTOM', 'CREAM', 2, 0],
            ],
            'KF-W-DR-001' => [
                ['S', 'MAROON', 3, 0],
                ['M', 'MAROON', 5, 1],
                ['L', 'GREEN', 2, 0],
                ['CUSTOM', 'CREAM', 1, 0],
            ],
            'KF-K-VS-001' => [
                ['S', 'BLACK', 4, 0],
                ['M', 'BLACK', 3, 0],
                ['L', 'NAVY', 0, 0],
            ],
            'KF-M-WC-001' => [
                ['M', 'BLACK', 5, 0],
                ['L', 'BLACK', 4, 0],
                ['L', 'MAROON', 2, 0],
            ],
            'KF-A-KS-001' => [
                ['CUSTOM', 'MAROON', 5, 0],
                ['CUSTOM', 'CREAM', 6, 0],
                ['CUSTOM', 'GREEN', 4, 0],
            ],
        ];

        foreach ($definitions as $productSku => $variants) {
            $product = $products->get($productSku);

            if (! $product) {
                continue;
            }

            $variantSkus = [];
            $totalOnHand = 0;

            foreach ($variants as $index => [$sizeCode, $colorCode, $onHand, $reserved]) {
                $size = $sizes->get($sizeCode);
                $color = $colors->get($colorCode);
                $variantSku = $productSku.'-'.$sizeCode.'-'.$colorCode;
                $variantSkus[] = $variantSku;
                $totalOnHand += $onHand;

                $variant = $product->variants()->updateOrCreate(
                    ['sku' => $variantSku],
                    [
                        'size_id' => $size->id,
                        'color_id' => $color->id,
                        'option_key' => $sizeCode.'-'.$colorCode,
                        'barcode' => null,
                        'price_minor' => $product->price_minor,
                        'sale_price_minor' => null,
                        'is_active' => true,
                        'sort_order' => ($index + 1) * 10,
                    ],
                );

                $variant->inventory()->updateOrCreate(
                    [],
                    [
                        'quantity_on_hand' => $onHand,
                        'quantity_reserved' => $reserved,
                        'low_stock_threshold' => 2,
                    ],
                );
            }

            $product->variants()->whereNotIn('sku', $variantSkus)->delete();
            $product->update(['stock_quantity' => $totalOnHand]);
        }
    }

    private function seedCollections($products): void
    {
        $definitions = [
            10 => [
                'translations' => [
                    'en' => ['New Arrivals', 'new-arrivals', 'Fresh KabulFit pieces and newly prepared Afghan styles.'],
                    'fa' => ['تازه‌رسیده‌ها', 'تازه-رسیده-ها', 'جدیدترین لباس‌ها و طرح‌های افغانی آماده‌شده در کابل‌فیت.'],
                    'ps' => ['نوي راغلي', 'نوي-راغلي', 'د کابل‌فټ تازه افغان لباسونه او نوي چمتو شوي سټایلونه.'],
                ],
                'products' => ['KF-A-KS-001', 'KF-M-WC-001', 'KF-W-DR-001'],
            ],
            20 => [
                'translations' => [
                    'en' => ['Wedding Edit', 'wedding-edit', 'Formal Afghan clothing selected for weddings and celebrations.'],
                    'fa' => ['انتخاب عروسی', 'انتخاب-عروسی', 'لباس‌های رسمی افغانی برای عروسی‌ها و جشن‌ها.'],
                    'ps' => ['د واده ټولګه', 'د-واده-ټولګه', 'د ودونو او خوښیو لپاره غوره شوي رسمي افغان کالي.'],
                ],
                'products' => ['KF-W-DR-001', 'KF-M-PT-001', 'KF-M-WC-001'],
            ],
            30 => [
                'translations' => [
                    'en' => ['Heritage Essentials', 'heritage-essentials', 'Core Afghan wardrobe pieces inspired by enduring regional traditions.'],
                    'fa' => ['ضروریات میراث', 'ضروریات-میراث', 'لباس‌های اساسی افغانی با الهام از سنت‌های ماندگار منطقه‌ای.'],
                    'ps' => ['د میراث بنسټیز توکي', 'د-میراث-بنسټیز-توکي', 'د تلپاتې سیمه‌ییزو دودونو څخه الهام اخیستي بنسټیز افغان کالي.'],
                ],
                'products' => ['KF-M-PT-001', 'KF-W-DR-001', 'KF-K-VS-001', 'KF-M-WC-001', 'KF-A-KS-001'],
            ],
        ];

        foreach ($definitions as $sortOrder => $definition) {
            $collection = Collection::query()->updateOrCreate(
                ['sort_order' => $sortOrder],
                ['is_active' => true],
            );

            foreach ($definition['translations'] as $locale => [$name, $slug, $description]) {
                $collection->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'name' => $name,
                        'slug' => $slug,
                        'description' => $description,
                        'seo_title' => $name.' | KabulFit',
                        'seo_description' => $description,
                    ],
                );
            }

            $sync = [];
            foreach ($definition['products'] as $index => $sku) {
                if ($product = $products->get($sku)) {
                    $sync[$product->id] = ['sort_order' => ($index + 1) * 10];
                }
            }

            $collection->products()->sync($sync);
        }
    }

    private function seedMedia($products): void
    {
        foreach ($products as $product) {
            $media = $product->media()->updateOrCreate(
                ['path' => 'images/catalog/kabulfit-product-placeholder.svg'],
                [
                    'mime_type' => 'image/svg+xml',
                    'width' => 900,
                    'height' => 1100,
                    'sort_order' => 10,
                    'is_primary' => true,
                ],
            );

            foreach ($product->translations as $translation) {
                $media->translations()->updateOrCreate(
                    ['locale' => $translation->locale],
                    ['alt_text' => $translation->name.' — KabulFit'],
                );
            }
        }
    }

    private function seedRelatedProducts($products): void
    {
        $definitions = [
            'KF-M-PT-001' => ['KF-M-WC-001', 'KF-W-DR-001'],
            'KF-W-DR-001' => ['KF-A-KS-001', 'KF-M-PT-001'],
            'KF-K-VS-001' => ['KF-M-WC-001'],
            'KF-M-WC-001' => ['KF-M-PT-001', 'KF-A-KS-001'],
            'KF-A-KS-001' => ['KF-W-DR-001', 'KF-M-WC-001'],
        ];

        foreach ($definitions as $sku => $relatedSkus) {
            $product = $products->get($sku);

            if (! $product) {
                continue;
            }

            $sync = [];
            foreach ($relatedSkus as $index => $relatedSku) {
                if ($related = $products->get($relatedSku)) {
                    $sync[$related->id] = ['sort_order' => ($index + 1) * 10];
                }
            }

            $product->relatedProducts()->sync($sync);
        }
    }
}
