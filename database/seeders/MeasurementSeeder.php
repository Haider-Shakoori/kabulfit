<?php

namespace Database\Seeders;

use App\Models\MeasurementDefinition;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MeasurementSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $defs = [
                ['perahan_tunban', 'chest', 60, 180, 10, ['en' => ['Chest', 'Measure around the fullest part of the chest.'], 'fa' => ['سینه', 'دور پُرترین قسمت سینه را اندازه بگیرید.'], 'ps' => ['سینه', 'د سینې د تر ټولو پراخې برخې شاوخوا اندازه واخلئ.']]],
                ['perahan_tunban', 'shoulder', 25, 70, 20, ['en' => ['Shoulder', 'Measure shoulder point to shoulder point across the back.'], 'fa' => ['شانه', 'از نقطه یک شانه تا شانه دیگر از پشت اندازه بگیرید.'], 'ps' => ['اوږه', 'له یوې اوږې تر بلې اوږې د شا له خوا اندازه واخلئ.']]],
                ['perahan_tunban', 'sleeve', 35, 90, 30, ['en' => ['Sleeve', 'Measure from shoulder point to desired cuff length.'], 'fa' => ['آستین', 'از شانه تا طول دلخواه سرآستین اندازه بگیرید.'], 'ps' => ['لستوڼی', 'له اوږې څخه د لستوڼي تر مطلوب پای پورې اندازه واخلئ.']]],
                ['perahan_tunban', 'shirt_length', 60, 150, 40, ['en' => ['Shirt length', 'Measure from the high shoulder to the desired hem.'], 'fa' => ['قد پیراهن', 'از بالای شانه تا قد دلخواه پیراهن اندازه بگیرید.'], 'ps' => ['د کمیس اوږدوالی', 'له لوړې اوږې تر مطلوب لمن پورې اندازه واخلئ.']]],
                ['perahan_tunban', 'waist', 50, 180, 50, ['en' => ['Waist', 'Measure comfortably around the natural waist.'], 'fa' => ['کمر', 'دور کمر طبیعی را بدون فشار اندازه بگیرید.'], 'ps' => ['ملا', 'د طبیعي ملا شاوخوا په آرامه اندازه واخلئ.']]],
                ['perahan_tunban', 'trouser_length', 60, 140, 60, ['en' => ['Trouser length', 'Measure from waist to desired trouser hem.'], 'fa' => ['قد تنبان', 'از کمر تا پایین دلخواه تنبان اندازه بگیرید.'], 'ps' => ['د پرتوګ اوږدوالی', 'له ملا تر مطلوب پای پورې اندازه واخلئ.']]],
                ['dress', 'bust', 55, 180, 10, ['en' => ['Bust', 'Measure around the fullest part of the bust.'], 'fa' => ['دور سینه', 'دور پُرترین قسمت سینه را اندازه بگیرید.'], 'ps' => ['د سینې چاپېر', 'د سینې د تر ټولو پراخې برخې شاوخوا اندازه واخلئ.']]],
                ['dress', 'waist', 45, 170, 20, ['en' => ['Waist', 'Measure around the natural waist.'], 'fa' => ['کمر', 'دور کمر طبیعی را اندازه بگیرید.'], 'ps' => ['ملا', 'د طبیعي ملا شاوخوا اندازه واخلئ.']]],
                ['dress', 'hips', 60, 190, 30, ['en' => ['Hips', 'Measure around the fullest part of the hips.'], 'fa' => ['باسن', 'دور پُرترین قسمت باسن را اندازه بگیرید.'], 'ps' => ['کوناټي', 'د کوناټو د تر ټولو پراخې برخې شاوخوا اندازه واخلئ.']]],
                ['dress', 'dress_length', 70, 180, 40, ['en' => ['Dress length', 'Measure from high shoulder to desired hem.'], 'fa' => ['قد لباس', 'از بالای شانه تا قد دلخواه لباس اندازه بگیرید.'], 'ps' => ['د جامې اوږدوالی', 'له لوړې اوږې تر مطلوب لمن پورې اندازه واخلئ.']]],
                ['waistcoat', 'chest', 55, 180, 10, ['en' => ['Chest', 'Measure around the fullest part of the chest.'], 'fa' => ['سینه', 'دور پُرترین قسمت سینه را اندازه بگیرید.'], 'ps' => ['سینه', 'د سینې د تر ټولو پراخې برخې شاوخوا اندازه واخلئ.']]],
                ['waistcoat', 'shoulder', 20, 70, 20, ['en' => ['Shoulder', 'Measure shoulder point to shoulder point.'], 'fa' => ['شانه', 'از یک نقطه شانه تا نقطه دیگر اندازه بگیرید.'], 'ps' => ['اوږه', 'له یوې اوږې تر بلې اوږې اندازه واخلئ.']]],
                ['waistcoat', 'waistcoat_length', 35, 100, 30, ['en' => ['Waistcoat length', 'Measure from high shoulder to desired waistcoat hem.'], 'fa' => ['قد واسکت', 'از بالای شانه تا پایین دلخواه واسکت اندازه بگیرید.'], 'ps' => ['د واسکټ اوږدوالی', 'له لوړې اوږې تر مطلوب پای پورې اندازه واخلئ.']]],
            ];
            foreach ($defs as [$garment,$code,$min,$max,$sort,$translations]) {
                $d = MeasurementDefinition::updateOrCreate(['garment_type' => $garment, 'code' => $code], ['uuid' => (string) Str::uuid(), 'min_cm' => $min, 'max_cm' => $max, 'step_cm' => .1, 'is_required' => true, 'is_active' => true, 'sort_order' => $sort]);
                foreach ($translations as $locale => [$name,$instructions]) {
                    $d->translations()->updateOrCreate(['locale' => $locale], ['name' => $name, 'instructions' => $instructions, 'guide_image_path' => 'images/measurements/'.str_replace('_', '-', $garment).'.svg']);
                }
            }
            Product::where('sku', 'KF-M-PT-001')->update(['tailoring_enabled' => true, 'measurement_garment_type' => 'perahan_tunban']);
            Product::where('sku', 'KF-W-DR-001')->update(['tailoring_enabled' => true, 'measurement_garment_type' => 'dress']);
            Product::where('sku', 'KF-M-WC-001')->update(['tailoring_enabled' => true, 'measurement_garment_type' => 'waistcoat']);
        });
    }
}
