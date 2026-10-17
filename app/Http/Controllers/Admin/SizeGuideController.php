<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SizeGuide;
use App\Services\Admin\AuditService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SizeGuideController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('products.manage'), 403);

        return view('admin.size-guides.index', [
            'guides' => SizeGuide::query()->orderByDesc('display_order')->orderByDesc('id')->get(),
            'categories' => Category::query()->where('is_active', true)->with(['translations', 'parent.translations'])->orderBy('sort_order')->get(),
            'seo' => PrivatePageSeo::make('Admin Size Guides', route('admin.size-guides.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('products.manage'), 403);
        $guide = SizeGuide::query()->create($this->validated($request));

        $audit->record($request->user(), 'size_guide.created', $guide, ['size_guide_id' => $guide->id]);

        return back()->with('status', 'Size guide created.');
    }

    public function update(Request $request, string $locale, SizeGuide $sizeGuide, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('products.manage'), 403);
        $before = $sizeGuide->getAttributes();
        $sizeGuide->update($this->validated($request));

        $audit->record($request->user(), 'size_guide.updated', $sizeGuide, [
            'size_guide_id' => $sizeGuide->id,
            'before' => $before,
            'after' => $sizeGuide->fresh()->getAttributes(),
        ]);

        return back()->with('status', 'Size guide updated.');
    }

    public function destroy(Request $request, string $locale, SizeGuide $sizeGuide, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('products.manage'), 403);
        $audit->record($request->user(), 'size_guide.deleted', $sizeGuide, ['size_guide_id' => $sizeGuide->id]);
        $sizeGuide->delete();

        return back()->with('status', 'Size guide deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_pashto' => ['nullable', 'string', 'max:255'],
            'title_dari' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'subcategory' => ['nullable', 'string', 'max:255'],
            'image_url' => ['required', 'url', 'max:2048'],
            'image_url_pashto' => ['nullable', 'url', 'max:2048'],
            'image_url_dari' => ['nullable', 'url', 'max:2048'],
            'description' => ['nullable', 'string', 'max:5000'],
            'description_pashto' => ['nullable', 'string', 'max:5000'],
            'description_dari' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['nullable', 'boolean'],
            'display_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ]) + [
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
