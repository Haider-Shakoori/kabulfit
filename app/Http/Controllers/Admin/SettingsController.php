<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Admin\AuditService;
use App\Services\Settings\SiteSettings;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(string $locale, SiteSettings $settings): View
    {
        $this->authorize('viewAny', Setting::class);

        return view('admin.settings.index', [
            'values' => [
                'contact_email' => $settings->get('site.contact_email', 'info@kabulfit.com'),
                'facebook_url' => $settings->get('social.facebook', 'https://www.facebook.com/KabulFitTailoring/'),
                'instagram_url' => $settings->get('social.instagram', ''),
                'tiktok_url' => $settings->get('social.tiktok', ''),
                'youtube_url' => $settings->get('social.youtube', 'http://www.youtube.com/@Kabulfit'),
                'whatsapp_url' => $settings->get('social.whatsapp', 'https://wa.me/93794120017'),
                'meta_pixel_id' => $settings->get('analytics.meta_pixel_id', (string) config('services.meta.pixel_id')),
                'ga_measurement_id' => $settings->get('analytics.ga_measurement_id', (string) config('services.analytics.measurement_id')),
                'titles' => collect(config('kabulfit.supported_locales'))->mapWithKeys(fn ($locale) => [$locale => $settings->get('seo.home.title.'.$locale, '')])->all(),
                'descriptions' => collect(config('kabulfit.supported_locales'))->mapWithKeys(fn ($locale) => [$locale => $settings->get('seo.home.description.'.$locale, '')])->all(),
            ],
            'seo' => PrivatePageSeo::make('Admin Settings', route('admin.settings.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function update(Request $request, string $locale, SiteSettings $settings, AuditService $audit): RedirectResponse
    {
        $this->authorize('viewAny', Setting::class);
        $data = $request->validate([
            'contact_email' => 'required|email|max:255',
            'facebook_url' => 'nullable|url|max:2048',
            'instagram_url' => 'nullable|url|max:2048',
            'tiktok_url' => 'nullable|url|max:2048',
            'youtube_url' => 'nullable|url|max:2048',
            'whatsapp_url' => 'nullable|url|max:2048',
            'meta_pixel_id' => 'nullable|string|max:64',
            'ga_measurement_id' => ['nullable', 'string', 'max:64', 'regex:/^G-[A-Z0-9]+$/i'],
            'titles' => 'required|array',
            'titles.*' => 'required|string|max:255',
            'descriptions' => 'required|array',
            'descriptions.*' => 'required|string|max:500',
        ]);

        $settings->put('general', 'site.contact_email', $data['contact_email']);
        $settings->put('social', 'social.facebook', $data['facebook_url'] ?? null);
        $settings->put('social', 'social.instagram', $data['instagram_url'] ?? null);
        $settings->put('social', 'social.tiktok', $data['tiktok_url'] ?? null);
        $settings->put('social', 'social.youtube', $data['youtube_url'] ?? null);
        $settings->put('social', 'social.whatsapp', $data['whatsapp_url'] ?? null);
        $settings->put('analytics', 'analytics.meta_pixel_id', $data['meta_pixel_id'] ?? null);
        $settings->put('analytics', 'analytics.ga_measurement_id', $data['ga_measurement_id'] ?? null);

        foreach (config('kabulfit.supported_locales') as $locale) {
            $settings->put('seo', 'seo.home.title.'.$locale, $data['titles'][$locale] ?? '');
            $settings->put('seo', 'seo.home.description.'.$locale, $data['descriptions'][$locale] ?? '');
        }

        $audit->record($request->user(), 'settings.updated', null, ['keys' => [
            'site.contact_email',
            'social.*',
            'analytics.*',
            'seo.home.title.*',
            'seo.home.description.*',
        ]]);

        return back()->with('status', 'Settings updated.');
    }
}
