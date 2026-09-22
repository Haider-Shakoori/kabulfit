<?php

namespace Database\Seeders;

use App\Models\MeasurementDefinition;
use App\Models\Product;
use Illuminate\Database\Seeder;

class MeasurementSeeder extends Seeder
{
    public function run(): void
    {
        $garments = [
            'perahan_tunban' => [
                'guide' => 'images/measurements/perahan-tunban.svg',
                'definitions' => [
                    ['neck', 25, 60, 'Neck', 'دور گردن', 'غاړه'],
                    ['shoulder', 30, 65, 'Shoulder', 'شانه', 'اوږه'],
                    ['chest', 60, 160, 'Chest', 'سینه', 'سینه'],
                    ['sleeve_length', 35, 90, 'Sleeve length', 'طول آستین', 'د لستوڼي اوږدوالی'],
                    ['shirt_length', 60, 140, 'Shirt length', 'طول پیراهن', 'د کمیس اوږدوالی'],
                    ['waist', 50, 160, 'Waist', 'کمر', 'کمر'],
                    ['trouser_length', 70, 130, 'Trouser length', 'طول تنبان', 'د پرتوګ اوږدوالی'],
                    ['inseam', 45, 100, 'Inseam', 'طول داخل پا', 'د پښې دننه اوږدوالی'],
                ],
            ],
            'dress' => [
                'guide' => 'images/measurements/dress.svg',
                'definitions' => [
                    ['shoulder', 30, 60, 'Shoulder', 'شانه', 'اوږه'],
                    ['bust', 60, 160, 'Bust', 'دور سینه', 'د سینې اندازه'],
                    ['waist', 50, 150, 'Waist', 'کمر', 'کمر'],
                    ['hip', 70, 180, 'Hip', 'باسن', 'ورون'],
                    ['sleeve_length', 35, 90, 'Sleeve length', 'طول آستین', 'د لستوڼي اوږدوالی'],
                    ['dress_length', 80, 180, 'Dress length', 'طول لباس', 'د لباس اوږدوالی'],
                ],
            ],
            'waistcoat' => [
                'guide' => 'images/measurements/waistcoat.svg',
                'definitions' => [
                    ['shoulder', 25, 60, 'Shoulder', 'شانه', 'اوږه'],
                    ['chest', 50, 150, 'Chest', 'سینه', 'سینه'],
                    ['waist', 45, 150, 'Waist', 'کمر', 'کمر'],
                    ['waistcoat_length', 35, 100, 'Waistcoat length', 'طول واسکت', 'د واسکټ اوږدوالی'],
                ],
            ],
        ];

        $sequence = 1;

        foreach ($garments as $garmentType => $garment) {
            foreach ($garment['definitions'] as $sort => [$code, $min, $max, $en, $fa, $ps]) {
                $uuid = sprintf('60000000-0000-4000-8000-%012d', $sequence++);

                $definition = MeasurementDefinition::query()->updateOrCreate(
                    ['garment_type' => $garmentType, 'code' => $code],
                    [
                        'uuid' => $uuid,
                        'min_cm' => $min,
                        'max_cm' => $max,
                        'step_cm' => 0.10,
                        'is_required' => true,
                        'is_active' => true,
                        'sort_order' => ($sort + 1) * 10,
                    ],
                );

                foreach ([
                    'en' => [$en, "Measure {$en} comfortably without pulling the tape tight."],
                    'fa' => [$fa, "اندازه {$fa} را بدون کشیدن نوار اندازه‌گیری ثبت کنید."],
                    'ps' => [$ps, "د {$ps} اندازه د متر له کلک راکاږلو پرته ثبت کړئ."],
                ] as $locale => [$name, $instructions]) {
                    $definition->translations()->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'name' => $name,
                            'instructions' => $instructions,
                            'guide_image_path' => $garment['guide'],
                        ],
                    );
                }
            }
        }

        $productMapping = [
            'KF-M-PT-001' => 'perahan_tunban',
            'KF-W-DR-001' => 'dress',
            'KF-K-VS-001' => 'waistcoat',
            'KF-M-WC-001' => 'waistcoat',
        ];

        foreach ($productMapping as $sku => $garmentType) {
            Product::query()->where('sku', $sku)->update([
                'tailoring_enabled' => true,
                'measurement_garment_type' => $garmentType,
            ]);
        }
    }
}
