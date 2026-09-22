<?php

namespace App\Services\Settings;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SiteSettings
{
    public function get(string $key, ?string $default = null): ?string
    {
        return Cache::remember(
            'site-setting:'.$key,
            now()->addMinutes(10),
            fn () => Setting::query()->where('key', $key)->value('value') ?? $default,
        );
    }

    public function put(string $group, string $key, ?string $value): Setting
    {
        $setting = Setting::query()->updateOrCreate(
            ['key' => $key],
            [
                'uuid' => Setting::query()->where('key', $key)->value('uuid') ?? (string) Str::uuid(),
                'group' => $group,
                'value' => $value,
            ],
        );

        Cache::forget('site-setting:'.$key);

        return $setting;
    }
}
