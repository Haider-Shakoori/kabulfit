<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
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
                'fa' => ['اکسسوری', 'اکسسوری', 'شال‌ها و تکمیل‌کننده‌های الهام‌گرفته از پوشاک افغانی.'],
                'ps' => ['اکسسوري', 'اکسسوري', 'افغان الهام لرونکي شالونه او د دودیز لباس بشپړوونکي توکي.'],
            ]);

            $this->product($men->id, 'KF-M-PT-001', 650000, true, 10, [
                'en' => ['Classic Afghan Perahan Tunban', 'classic-afghan-perahan-tunban', 'A refined made-to-measure Perahan Tunban inspired by Afghan tailoring traditions.'],
                'fa' => ['پیرهن تنبان کلاسیک افغانی', 'پیرهن-تنبان-کلاسیک-افغانی', 'پیرهن تنبان شیک با دوخت سفارشی و الهام‌گرفته از خیاطی اصیل افغانی.'],
                'ps' => ['کلاسیک افغان پیرهن تنبان', 'کلاسیک-افغان-پیرهن-تنبان', 'د افغان دودیز خیاطۍ پر بنسټ په اندازه ګنډل شوی ښکلی پیرهن تنبان.'],
            ]);

            $this->product($women->id, 'KF-W-DR-001', 980000, true, 20, [
                'en' => ['Herat Embroidered Afghan Dress', 'herat-embroidered-afghan-dress', 'A formal Afghan dress featuring Herat-inspired embroidery and tailored finishing.'],
                'fa' => ['لباس افغانی خامک‌دوزی هرات', 'لباس-افغانی-خامک-دوزی-هرات', 'لباس مجلسی افغانی با خامک‌دوزی الهام‌گرفته از هنر هرات و دوخت حرفه‌ای.'],
                'ps' => ['د هرات ګنډل شوی افغان لباس', 'د-هرات-ګنډل-شوی-افغان-لباس', 'رسمي افغان لباس د هرات له هنر الهام اخیستل شوي ګنډلو او مسلکي جوړښت سره.'],
            ]);

            $this->product($kids->id, 'KF-K-VS-001', 420000, false, 30, [
                'en' => ['Kids Afghan Waistcoat Set', 'kids-afghan-waistcoat-set', 'A comfortable Afghan-inspired waistcoat set prepared for celebrations and family occasions.'],
                'fa' => ['ست واسکت افغانی اطفال', 'ست-واسکت-افغانی-اطفال', 'ست راحت واسکت افغانی برای جشن‌ها و محافل خانوادگی کودکان.'],
                'ps' => ['د ماشومانو افغان واسکټ سیټ', 'د-ماشومانو-افغان-واسکټ-سیټ', 'د ماشومانو د مېلو او کورنیو مراسمو لپاره ارام افغان واسکټ سیټ.'],
            ]);

            $this->product($men->id, 'KF-M-WC-001', 790000, true, 40, [
                'en' => ['Traditional Afghan Waistcoat', 'traditional-afghan-waistcoat', 'A structured Afghan waistcoat with geometric embroidery for formal and everyday layering.'],
                'fa' => ['واسکت سنتی افغانی', 'واسکت-سنتی-افغانی', 'واسکت ساختارمند افغانی با نقش‌های هندسی برای محافل رسمی و استفاده روزمره.'],
                'ps' => ['دودیز افغان واسکټ', 'دودیز-افغان-واسکټ', 'د رسمي او ورځني اغوستلو لپاره هندسي ګنډل شوی افغان واسکټ.'],
            ]);

            $this->product($accessories->id, 'KF-A-KS-001', 480000, true, 50, [
                'en' => ['Kuchi-Inspired Afghan Shawl', 'kuchi-inspired-afghan-shawl', 'A versatile shawl inspired by Kuchi color, textile and decorative traditions.'],
                'fa' => ['شال افغانی الهام‌گرفته از کوچی', 'شال-افغانی-الهام-گرفته-از-کوچی', 'شال چندمنظوره با الهام از رنگ، پارچه و تزئینات سنت کوچی.'],
                'ps' => ['له کوچي دود الهام اخیستی افغان شال', 'له-کوچي-دود-الهام-اخیستی-افغان-شال', 'د کوچي رنګونو، ټوکر او سینګار له دود څخه الهام اخیستی شال.'],
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

    private function product(int $categoryId, string $sku, int $priceMinor, bool $featured, int $sortOrder, array $translations): Product
    {
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
}
