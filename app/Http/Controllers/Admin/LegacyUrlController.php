<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegacyUrl;
use App\Services\Admin\AuditService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LegacyUrlController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', LegacyUrl::class);

        return view('admin.legacy.index', [
            'entries' => LegacyUrl::query()->orderBy('legacy_path')->paginate(50),
            'seo' => PrivatePageSeo::make('Legacy URL Inventory', route('admin.legacy.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function update(Request $request, string $locale, LegacyUrl $legacyUrl, AuditService $audit): RedirectResponse
    {
        $this->authorize('update', $legacyUrl);
        $data = $request->validate([
            'disposition' => 'required|in:redirect,manual_product,private,gone',
            'target_path' => 'nullable|string|max:2048|starts_with:/',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:5000',
        ]);

        $before = $legacyUrl->only(['disposition', 'target_path', 'is_active', 'notes']);
        $legacyUrl->update([
            'disposition' => $data['disposition'],
            'target_path' => $data['target_path'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
            'notes' => $data['notes'] ?? null,
        ]);

        $audit->record($request->user(), 'legacy_url.updated', $legacyUrl, [
            'legacy_path' => $legacyUrl->legacy_path,
            'before' => $before,
            'after' => $legacyUrl->fresh()->only(array_keys($before)),
        ]);

        return back()->with('status', 'Legacy URL mapping updated.');
    }
}
