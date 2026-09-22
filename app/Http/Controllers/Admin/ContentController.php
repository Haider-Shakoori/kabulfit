<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Models\ContentPage;
use App\Models\ContentPageTranslation;
use App\Services\Admin\AuditService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ContentController extends Controller
{
    private const RESERVED_PAGE_SLUGS = [
        'shop',
        'categories',
        'collections',
        'products',
        'blog',
        'login',
        'register',
        'forgot-password',
        'reset-password',
        'verify-email',
        'account',
        'measurements',
        'tailoring',
        'tailor',
        'cart',
        'wishlist',
        'checkout',
        'orders',
        'admin',
    ];

    public function index(): View
    {
        $this->authorize('viewAny', ContentPage::class);

        return view('admin.content.index', [
            'pages' => ContentPage::query()->with('translations')->orderBy('sort_order')->get(),
            'posts' => BlogPost::query()->with('translations')->latest()->get(),
            'seo' => PrivatePageSeo::make('Admin Content', route('admin.content.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function updatePage(Request $request, string $locale, ContentPage $page, AuditService $audit): RedirectResponse
    {
        $this->authorize('update', $page);
        $data = $this->contentData($request);
        $this->validatePageSlugs($data['translations'], $page);

        DB::transaction(function () use ($page, $data): void {
            $page->update(['is_published' => (bool) ($data['is_published'] ?? false)]);
            $this->syncTranslations($page, $data['translations']);
        });

        $audit->record($request->user(), 'content.page_updated', $page, ['page_key' => $page->page_key]);

        return back()->with('status', 'Content page updated.');
    }

    public function storePost(Request $request, AuditService $audit): RedirectResponse
    {
        $this->authorize('create', BlogPost::class);
        $data = $this->contentData($request);
        $this->validatePostSlugs($data['translations']);

        $post = DB::transaction(function () use ($request, $data): BlogPost {
            $post = BlogPost::query()->create([
                'uuid' => (string) Str::uuid(),
                'author_id' => $request->user()->id,
                'is_published' => (bool) ($data['is_published'] ?? false),
                'published_at' => ($data['is_published'] ?? false) ? now() : null,
            ]);
            $this->syncTranslations($post, $data['translations']);

            return $post;
        });

        $audit->record($request->user(), 'content.post_created', $post, ['post_uuid' => $post->uuid]);

        return back()->with('status', 'Blog post created.');
    }

    public function updatePost(Request $request, string $locale, BlogPost $post, AuditService $audit): RedirectResponse
    {
        $this->authorize('update', $post);
        $data = $this->contentData($request);
        $this->validatePostSlugs($data['translations'], $post);

        DB::transaction(function () use ($post, $data): void {
            $published = (bool) ($data['is_published'] ?? false);
            $post->update([
                'is_published' => $published,
                'published_at' => $published ? ($post->published_at ?? now()) : null,
            ]);
            $this->syncTranslations($post, $data['translations']);
        });

        $audit->record($request->user(), 'content.post_updated', $post, ['post_uuid' => $post->uuid]);

        return back()->with('status', 'Blog post updated.');
    }

    public function destroyPost(Request $request, string $locale, BlogPost $post, AuditService $audit): RedirectResponse
    {
        $this->authorize('delete', $post);
        $uuid = $post->uuid;
        $audit->record($request->user(), 'content.post_deleted', $post, ['post_uuid' => $uuid]);
        $post->delete();

        return back()->with('status', 'Blog post deleted.');
    }

    private function contentData(Request $request): array
    {
        return $request->validate([
            'is_published' => 'nullable|boolean',
            'translations' => 'required|array',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.slug' => 'required|string|max:255',
            'translations.*.excerpt' => 'nullable|string|max:1000',
            'translations.*.body' => 'required|string|max:50000',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_description' => 'nullable|string|max:320',
        ]);
    }

    private function validatePageSlugs(array $translations, ContentPage $page): void
    {
        foreach ($translations as $locale => $translation) {
            $slug = trim((string) $translation['slug']);

            if (in_array($slug, self::RESERVED_PAGE_SLUGS, true)) {
                throw ValidationException::withMessages([
                    "translations.{$locale}.slug" => 'This slug is reserved by the application.',
                ]);
            }

            $exists = ContentPageTranslation::query()
                ->where('locale', $locale)
                ->where('slug', $slug)
                ->where('content_page_id', '!=', $page->id)
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    "translations.{$locale}.slug" => 'This page slug is already in use for this language.',
                ]);
            }
        }
    }

    private function validatePostSlugs(array $translations, ?BlogPost $post = null): void
    {
        foreach ($translations as $locale => $translation) {
            $slug = trim((string) $translation['slug']);

            $query = BlogPostTranslation::query()
                ->where('locale', $locale)
                ->where('slug', $slug);

            if ($post) {
                $query->where('blog_post_id', '!=', $post->id);
            }

            if ($query->exists()) {
                throw ValidationException::withMessages([
                    "translations.{$locale}.slug" => 'This journal slug is already in use for this language.',
                ]);
            }
        }
    }

    private function syncTranslations(ContentPage|BlogPost $model, array $translations): void
    {
        foreach (config('kabulfit.supported_locales') as $translationLocale) {
            if (! isset($translations[$translationLocale])) {
                continue;
            }
            $model->translations()->updateOrCreate(
                ['locale' => $translationLocale],
                $translations[$translationLocale],
            );
        }
    }
}
