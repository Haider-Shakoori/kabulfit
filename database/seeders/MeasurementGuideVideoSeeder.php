<?php

namespace Database\Seeders;

use App\Models\MeasurementGuideVideo;
use Illuminate\Database\Seeder;

class MeasurementGuideVideoSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/base44_measurement_guide_videos.json');
        $rows = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);

        foreach ($rows as $row) {
            MeasurementGuideVideo::query()->updateOrCreate(
                [
                    'measurement_field' => $row['measurement_field'],
                    'category' => $row['category'],
                ],
                [
                    'title' => $row['title'] ?: null,
                    'video_url' => $row['video_url'],
                    'video_url_dari' => $row['video_url_dari'] ?: null,
                    'video_url_pashto' => $row['video_url_pashto'] ?: null,
                    'description' => $row['description'] ?: null,
                    'display_order' => (int) ($row['display_order'] ?? 0),
                    'is_active' => (bool) ($row['is_active'] ?? true),
                ],
            );
        }
    }
}
