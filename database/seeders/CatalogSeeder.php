<?php

namespace Database\Seeders;

use App\Models\CatalogCollection;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $men = $this->category(10, [
                'en' => ['Men', 'men', 'Traditional Afghan clothing and custom-tailored styles for men.'],
                'fa' => ['مردانه', 'مردانه', 'لباس‌های اصیل افغانی و دوخت سفارشی برای مردان.'],
                'ps' => ['نارینه', 'نارینه', 'د نارینه‌وو لپاره دودیزې افغان جامې او سفارشي ګنډنه.'],
            ]);

            $women = $this->category(20, [
                'en' => ['Women', 'women', 'Elegant Afghan dresses, embroidery and made-to-measure styles for women.'],
                'fa' => ['زنانه', 'زنانه', 'لباس‌های شیک افغانی، خامک‌دوزی و دوخت سفارشی برای بانوان.'],
                'ps' => ['ښځینه', 'ښځینه', 'د ښځو لپاره ښکلي افغان لباسونه، ګنډل او سفارشي کالي.'],
            ]);

            $kids = $this->category(30, [
                'en' => ['Kids', 'kids', 'Traditional Afghan outfits made for children.'],
                'fa' => ['اطفال', 'اطفال', 'لباس‌های سنتی افغانی برای کودکان.'],
                'ps' => ['ماشومان', 'ماشومان', 'د ماشومانو لپاره دودیزې افغان جامې.'],
            ]);

            $accessories = $this->category(40, [
                'en' => ['Accessories', 'accessories', 'Afghan-inspired shawls and finishing pieces for traditional outfits.'],
                'fa' => ['اکسسوری', 'اکسسوری', 'شال‌ها و لوازم الهام‌گرفته از پوشاک اصیل افغانی.'],
                'ps' => ['اکسسوري', 'اکسسوري', 'له افغان دودیزو جامو الهام اخیستي شالونه او لوازم.'],
            ]);

            $size = $this->option('size', 10, [
                'en' => 'Size', 'fa' => 'اندازه', 'ps' => 'اندازه',
            ], [
                'xs' => ['XS', 'XS', 'XS'],
                's' => ['S', 'S', 'S'],
                'm' => ['M', 'M', 'M'],
                'l' => ['L', 'L', 'L'],
                'xl' => ['XL', 'XL', 'XL'],
                'one-size' => ['One size', 'تک‌اندازه', 'یوه اندازه'],
            ]);

            $color = $this->option('color', 20, [
                'en' => 'Color', 'fa' => 'رنگ', 'ps' => 'رنګ',
            ], [
                'black' => ['Black', 'سیاه', 'تور'],
                'maroon' => ['Maroon', 'عنابی', 'زرغون سور'],
                'green' => ['Green', 'سبز', 'شین'],
                'cream' => ['Cream', 'کرم', 'کریم'],
                'navy' => ['Navy', 'سرمه‌ای', 'سمندري شین'],
            ]);

            $embroidery = $this->option('embroidery', 30, [
                'en' => 'Embroidery', 'fa' => 'خامک‌دوزی', 'ps' => 'ګنډل',
            ], [
                'plain' => ['Plain', 'ساده', 'ساده'],
                'hand' => ['Hand embroidered', 'خامک‌دوزی دستی', 'لاسي ګنډل'],
            ]);

            $options = compact('size', 'color', 'embroidery');

            $perahan = $this->product($men->id, 'KF-M-PT-001', 650000, true, 10, [
                'en' => ['Classic Afghan Perahan Tunban', 'classic-afghan-perahan-tunban', 'A refined made-to-measure Perahan Tunban inspired by Afghan tailoring traditions.'],
                'fa' => ['پیرهن تنبان کلاسیک افغانی', 'پیرهن-تنبان-کلاسیک-افغانی', 'پیرهن تنبان شیک با دوخت سفارشی و الهام‌گرفته از خیاطی اصیل افغانی.'],
                'ps' => ['کلاسیک افغان پیرهن تنبان', 'کلاسیک-افغان-پیرهن-تنبان', 'د افغان دودیز خیاطۍ پر بنسټ په اندازه ګنډل شوی ښکلی پیرهن تنبان.'],
            ]);

            $dress = $this->product($women->id, 'KF-W-DR-001', 980000, true, 20, [
                'en' => ['Hand-Embroidered Afghan Dress', 'hand-embroidered-afghan-dress', 'A formal Afghan dress featuring traditional-inspired embroidery and tailored finishing.'],
                'fa' => ['لباس افغانی خامک‌دوزی‌شده', 'لباس-افغانی-خامک-دوزی', 'لباس مجلسی افغانی با خامک‌دوزی الهام‌گرفته از هنر سنتی و دوخت حرفه‌ای.'],
                'ps' => ['لاس ګنډل شوی افغان لباس', 'لاس-ګنډل-شوی-افغان-لباس', 'رسمي افغان لباس له دودیز الهام اخیستل شوي ګنډلو او مسلکي جوړښت سره.'],
            ]);

            $kidsSet = $this->product($kids->id, 'KF-K-VS-001', 420000, false, 30, [
                'en' => ['Kids Afghan Waistcoat Set', 'kids-afghan-waistcoat-set', 'A comfortable Afghan-inspired waistcoat set prepared for celebrations and family occasions.'],
                'fa' => ['ست واسکت افغانی اطفال', 'ست-واسکت-افغانی-اطفال', 'ست راحت واسکت افغانی برای جشن‌ها و محافل خانوادگی کودکان.'],
                'ps' => ['د ماشومانو افغان واسکټ سیټ', 'د-ماشومانو-افغان-واسکټ-سیټ', 'د ماشومانو د مېلو او کورنیو مراسمو لپاره ارام افغان واسکټ سیټ.'],
            ]);

            $shawl = $this->product($accessories->id, 'KF-A-SH-001', 510000, true, 40, [
                'en' => ['Kuchi Embroidered Shawl', 'kuchi-embroidered-shawl', 'A richly detailed Afghan-inspired shawl designed to complement formal and traditional outfits.'],
                'fa' => ['شال کوچی خامک‌دوزی‌شده', 'شال-کوچی-خامک-دوزی', 'شال افغانی با جزئیات ظریف برای تکمیل لباس‌های مجلسی و سنتی.'],
                'ps' => ['کوشي ګنډل شوی شال', 'کوشي-ګنډل-شوی-شال', 'د رسمي او دودیزو جامو لپاره په ښکلو جزیاتو جوړ افغان شال.'],
            ]);

            $this->media($perahan, 'images/kabulfit-hero-textile.svg', 960, 1120, [
                'en' => 'Classic Afghan Perahan Tunban textile detail',
                'fa' => 'جزئیات پارچه پیرهن تنبان کلاسیک افغانی',
                'ps' => 'د کلاسیک افغان پیرهن تنبان د ټوکر جزیات',
            ]);
            $this->media($dress, 'images/kabulfit-craftsmanship.svg', 1200, 900, [
                'en' => 'Hand-embroidered Afghan dress craftsmanship detail',
                'fa' => 'جزئیات هنر لباس افغانی خامک‌دوزی‌شده',
                'ps' => 'د لاسي ګنډل شوي افغان لباس هنري جزیات',
            ]);
            $this->media($kidsSet, 'images/kabulfit-hero-textile.svg', 960, 1120, [
                'en' => 'Kids Afghan waistcoat textile detail',
                'fa' => 'جزئیات پارچه واسکت افغانی اطفال',
                'ps' => 'د ماشومانو افغان واسکټ د ټوکر جزیات',
            ]);
            $this->media($shawl, 'images/kabulfit-craftsmanship.svg', 1200, 900, [
                'en' => 'Kuchi embroidered shawl craftsmanship detail',
                'fa' => 'جزئیات هنر شال کوچی خامک‌دوزی‌شده',
                'ps' => 'د کوشي ګنډل شوي شال هنري جزیات',
            ]);

            $this->variant($perahan, 'S-BLK-H', 650000, 5, $options, ['size' => 's', 'color' => 'black', 'embroidery' => 'hand']);
            $this->variant($perahan, 'M-BLK-H', 650000, 4, $options, ['size' => 'm', 'color' => 'black', 'embroidery' => 'hand']);
            $this->variant($perahan, 'L-GRN-P', 620000, 3, $options, ['size' => 'l', 'color' => 'green', 'embroidery' => 'plain']);

            $this->variant($dress, 'S-MAR-H', 980000, 4, $options, ['size' => 's', 'color' => 'maroon', 'embroidery' => 'hand']);
            $this->variant($dress, 'M-MAR-H', 980000, 5, $options, ['size' => 'm', 'color' => 'maroon', 'embroidery' => 'hand']);
            $this->variant($dress, 'L-CRM-H', 1020000, 2, $options, ['size' => 'l', 'color' => 'cream', 'embroidery' => 'hand']);

            $this->variant($kidsSet, 'S-BLK-P', 420000, 5, $options, ['size' => 's', 'color' => 'black', 'embroidery' => 'plain']);
            $this->variant($kidsSet, 'M-GRN-H', 450000, 4, $options, ['size' => 'm', 'color' => 'green', 'embroidery' => 'hand']);

            $this->variant($shawl, 'ONE-CRM-H', 510000, 6, $options, ['size' => 'one-size', 'color' => 'cream', 'embroidery' => 'hand']);
            $this->variant($shawl, 'ONE-MAR-H', 530000, 3, $options, ['size' => 'one-size', 'color' => 'maroon', 'embroidery' => 'hand']);

            $perahan->update(['stock_quantity' => 12]);
            $dress->update(['stock_quantity' => 11]);
            $kidsSet->update(['stock_quantity' => 9]);
            $shawl->update(['stock_quantity' => 9]);

            $newArrivals = $this->collection(10, [
                'en' => ['New Arrivals', 'new-arrivals', 'Recently added KabulFit Afghan fashion pieces.'],
                'fa' => ['تازه‌رسیده‌ها', 'تازه-رسیده-ها', 'جدیدترین لباس‌ها و محصولات افغانی کابل‌فیت.'],
                'ps' => ['نوي راغلي', 'نوي-راغلي', 'د کابل‌فټ تازه اضافه شوي افغان فیشن محصولات.'],
            ]);
            $wedding = $this->collection(20, [
                'en' => ['Wedding Edit', 'wedding', 'Formal Afghan clothing and accessories selected for wedding occasions.'],
                'fa' => ['مجموعه عروسی', 'عروسی', 'لباس و اکسسوری افغانی برای محافل عروسی.'],
                'ps' => ['د واده ټولګه', 'واده', 'د واده لپاره غوره شوي افغان لباسونه او لوازم.'],
            ]);
            $heritage = $this->collection(30, [
                'en' => ['Heritage Essentials', 'heritage-essentials', 'Everyday expressions of enduring Afghan clothing traditions.'],
                'fa' => ['اصالت افغانی', 'اصالت-افغانی', 'انتخاب‌های ماندگار از سنت پوشاک افغانستان.'],
                'ps' => ['د میراث بنسټیز توکي', 'د-میراث-بنسټیز', 'د افغان جامو د تلپاتې دود غوره توکي.'],
            ]);

            $newArrivals->products()->syncWithoutDetaching([
                $dress->id => ['sort_order' => 10],
                $shawl->id => ['sort_order' => 20],
                $kidsSet->id => ['sort_order' => 30],
            ]);
            $wedding->products()->syncWithoutDetaching([
                $dress->id => ['sort_order' => 10],
                $shawl->id => ['sort_order' => 20],
            ]);
            $heritage->products()->syncWithoutDetaching([
                $perahan->id => ['sort_order' => 10],
                $kidsSet->id => ['sort_order' => 20],
            ]);
        });
    }

    private function category(int $sortOrder, array $translations): Category
    {
        $category = Category::query()->updateOrCreate(
            ['sort_order' => $sortOrder],
            ['is_active' => true],
        );

        foreach ($translations as $locale => [$name, $slug, $description]) {
            $category->translations()->updateOrCreate(
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

        return $category;
    }

    private function collection(int $sortOrder, array $translations): CatalogCollection
    {
        $collection = CatalogCollection::query()->updateOrCreate(
            ['sort_order' => $sortOrder],
            ['is_active' => true],
        );

        foreach ($translations as $locale => [$name, $slug, $description]) {
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

        return $collection;
    }

    private function option(string $code, int $sortOrder, array $names, array $values): ProductOption
    {
        $option = ProductOption::query()->updateOrCreate(
            ['code' => $code],
            ['is_filterable' => true, 'sort_order' => $sortOrder],
        );

        foreach ($names as $locale => $name) {
            $option->translations()->updateOrCreate(['locale' => $locale], ['name' => $name]);
        }

        $valueSort = 10;
        foreach ($values as $valueCode => [$en, $fa, $ps]) {
            $value = $option->values()->updateOrCreate(
                ['code' => $valueCode],
                ['sort_order' => $valueSort],
            );
            $value->translations()->updateOrCreate(['locale' => 'en'], ['name' => $en]);
            $value->translations()->updateOrCreate(['locale' => 'fa'], ['name' => $fa]);
            $value->translations()->updateOrCreate(['locale' => 'ps'], ['name' => $ps]);
            $valueSort += 10;
        }

        return $option;
    }

    private function product(
        int $categoryId,
        string $sku,
        int $priceMinor,
        bool $featured,
        int $sortOrder,
        array $translations,
    ): Product {
        $product = Product::query()->updateOrCreate(
            ['sku' => $sku],
            [
                'category_id' => $categoryId,
                'price_minor' => $priceMinor,
                'sale_price_minor' => null,
                'currency' => 'AFN',
                'stock_quantity' => 0,
                'is_active' => true,
                'is_featured' => $featured,
                'sort_order' => $sortOrder,
            ],
        );

        foreach ($translations as $locale => [$name, $slug, $description]) {
            $product->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'name' => $name,
                    'slug' => $slug,
                    'short_description' => $description,
                    'description' => $description,
                    'seo_title' => $name.' | KabulFit',
                    'seo_description' => $description,
                ],
            );
        }

        return $product;
    }

    private function media(Product $product, string $path, int $width, int $height, array $altTexts): void
    {
        $media = $product->media()->updateOrCreate(
            ['path' => $path],
            [
                'disk' => 'public',
                'mime_type' => 'image/svg+xml',
                'width' => $width,
                'height' => $height,
                'is_primary' => true,
                'sort_order' => 10,
            ],
        );

        foreach ($altTexts as $locale => $altText) {
            $media->translations()->updateOrCreate(['locale' => $locale], ['alt_text' => $altText]);
        }
    }

    private function variant(
        Product $product,
        string $suffix,
        int $priceMinor,
        int $stock,
        array $options,
        array $values,
    ): void {
        $variant = $product->variants()->updateOrCreate(
            ['sku' => $product->sku.'-'.$suffix],
            [
                'price_minor' => $priceMinor,
                'sale_price_minor' => null,
                'currency' => 'AFN',
                'is_active' => true,
                'sort_order' => $product->variants()->count() * 10 + 10,
            ],
        );

        $valueIds = collect($values)
            ->map(function (string $valueCode, string $optionCode) use ($options): int {
                /** @var ProductOption $option */
                $option = $options[$optionCode];

                /** @var ProductOptionValue $value */
                $value = $option->values()->where('code', $valueCode)->firstOrFail();

                return $value->id;
            })
            ->values()
            ->all();

        $variant->optionValues()->sync($valueIds);
        $variant->inventory()->updateOrCreate([], [
            'quantity_on_hand' => $stock,
            'reserved_quantity' => 0,
            'low_stock_threshold' => 2,
        ]);
    }
}
