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
    public function index(SiteSettings $settings): View
    {
        $this->authorize('viewAny', Setting::class);

        return view('admin.settings.index', [
            'values' => [
                'contact_email' => $settings->get('site.contact_email', 'info@kabulfit.com'),
                'titles' => collect(config('kabulfit.supported_locales'))->mapWithKeys(fn ($locale) => [$locale => $settings->get('seo.home.title.'.$locale, '')])->all(),
                'descriptions' => collect(config('kabulfit.supported_locales'))->mapWithKeys(fn ($locale) => [$locale => $settings->get('seo.home.description.'.$locale, '')])->all(),
            ],
            'seo' => PrivatePageSeo::make('Admin Settings', route('admin.settings.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function update(Request $request, SiteSettings $settings, AuditService $audit): RedirectResponse
    {
        $this->authorize('viewAny', Setting::class);
        $data = $request->validate([
            'contact_email' => 'required|email|max:255',
            'titles' => 'required|array',
            'titles.*' => 'required|string|max:255',
            'descriptions' => 'required|array',
            'descriptions.*' => 'required|string|max:500',
        ]);

        $settings->put('general', 'site.contact_email', $data['contact_email']);

        foreach (config('kabulfit.supported_locales') as $locale) {
            $settings->put('seo', 'seo.home.title.'.$locale, $data['titles'][$locale] ?? '');
            $settings->put('seo', 'seo.home.description.'.$locale, $data['descriptions'][$locale] ?? '');
        }

        $audit->record($request->user(), 'settings.updated', null, ['keys' => ['site.contact_email', 'seo.home.title.*', 'seo.home.description.*']]);

        return back()->with('status', 'Settings updated.');
    }
}
