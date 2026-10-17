<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Services\Admin\AuditService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('products.manage'), 403);

        return view('admin.categories.index', [
            'categories' => Category::query()
                ->with(['translations', 'parent.translations'])
                ->withCount('products')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
            'seo' => PrivatePageSeo::make('Admin Categories', route('admin.categories.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('products.manage'), 403);
        $data = $this->validated($request);

        $category = DB::transaction(function () use ($data): Category {
            $category = Category::query()->create([
                'parent_id' => $data['parent_id'] ?? null,
                'image_url' => $data['image_url'] ?? null,
                'sort_order' => $data['sort_order'],
                'is_active' => (bool) ($data['is_active'] ?? false),
            ]);

            $this->syncTranslations($category, $data['translations']);

            return $category;
        });

        $audit->record($request->user(), 'category.created', $category, ['category_id' => $category->id]);

        return back()->with('status', 'Category created.');
    }

    public function update(Request $request, string $locale, Category $category, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('products.manage'), 403);
        $data = $this->validated($request, $category);

        if (($data['parent_id'] ?? null) === $category->id) {
            throw ValidationException::withMessages(['parent_id' => 'A category cannot be its own parent.']);
        }

        $before = $category->only(['parent_id', 'image_url', 'sort_order', 'is_active']);

        DB::transaction(function () use ($category, $data): void {
            $category->update([
                'parent_id' => $data['parent_id'] ?? null,
                'image_url' => $data['image_url'] ?? null,
                'sort_order' => $data['sort_order'],
                'is_active' => (bool) ($data['is_active'] ?? false),
            ]);

            $this->syncTranslations($category, $data['translations']);
        });

        $audit->record($request->user(), 'category.updated', $category, [
            'category_id' => $category->id,
            'before' => $before,
            'after' => $category->fresh()->only(array_keys($before)),
        ]);

        return back()->with('status', 'Category updated.');
    }

    public function destroy(Request $request, string $locale, Category $category, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('products.manage'), 403);

        if ($category->products()->exists() || $category->children()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'Move products and child categories before deleting this category.',
            ]);
        }

        $audit->record($request->user(), 'category.deleted', $category, ['category_id' => $category->id]);
        $category->delete();

        return back()->with('status', 'Category deleted.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        $data = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
            'translations' => ['required', 'array'],
            'translations.*.name' => ['required', 'string', 'max:255'],
            'translations.*.slug' => ['required', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string', 'max:5000'],
        ]);

        foreach ($data['translations'] as $translationLocale => $translation) {
            $query = CategoryTranslation::query()
                ->where('locale', $translationLocale)
                ->where('slug', trim((string) $translation['slug']));

            if ($category) {
                $query->where('category_id', '!=', $category->id);
            }

            if ($query->exists()) {
                throw ValidationException::withMessages([
                    "translations.{$translationLocale}.slug" => 'This category slug is already in use for this language.',
                ]);
            }
        }

        return $data;
    }

    private function syncTranslations(Category $category, array $translations): void
    {
        foreach (config('kabulfit.supported_locales') as $translationLocale) {
            if (! isset($translations[$translationLocale])) {
                continue;
            }

            $category->translations()->updateOrCreate(
                ['locale' => $translationLocale],
                [
                    'name' => trim($translations[$translationLocale]['name']),
                    'slug' => trim($translations[$translationLocale]['slug']),
                    'description' => $translations[$translationLocale]['description'] ?? null,
                ],
            );
        }
    }
}
