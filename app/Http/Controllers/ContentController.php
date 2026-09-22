<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Models\ContentPageTranslation;
use App\Support\Seo\SeoData;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function page(string $locale, string $slug): View
    {
        $translation = ContentPageTranslation::query()
            ->where('locale', $locale)
            ->where('slug', $slug)
            ->whereHas('page', fn ($query) => $query->where('is_published', true))
            ->with('page.translations')
            ->firstOrFail();

        $alternates = $translation->page->translations
            ->mapWithKeys(fn ($item) => [$item->locale => route('content.page', ['locale' => $item->locale, 'slug' => $item->slug])])
            ->all();

        return view('content.page', [
            'translation' => $translation,
            'seo' => new SeoData(
                $translation->seo_title ?: $translation->title.' | KabulFit',
                $translation->seo_description ?: ($translation->excerpt ?? ''),
                route('content.page', ['locale' => $locale, 'slug' => $translation->slug]),
                $alternates,
                'index,follow',
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebPage',
                    'name' => $translation->title,
                    'description' => $translation->seo_description ?: $translation->excerpt,
                    'url' => route('content.page', ['locale' => $locale, 'slug' => $translation->slug]),
                    'isPartOf' => ['@type' => 'WebSite', 'name' => 'KabulFit', 'url' => route('home', ['locale' => $locale])],
                ],
            ),
        ]);
    }

    public function blog(string $locale): View
    {
        $posts = BlogPost::query()
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->whereHas('translations', fn ($query) => $query->where('locale', $locale))
            ->with('translations')
            ->latest('published_at')
            ->paginate(12);

        $alternates = collect(config('kabulfit.supported_locales'))
            ->mapWithKeys(fn ($item) => [$item => route('blog.index', ['locale' => $item])])
            ->all();

        return view('content.blog-index', [
            'posts' => $posts,
            'seo' => new SeoData(
                __('content.blog_title'),
                __('content.blog_description'),
                route('blog.index', ['locale' => $locale]),
                $alternates,
                'index,follow',
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'Blog',
                    'name' => __('content.blog_title'),
                    'url' => route('blog.index', ['locale' => $locale]),
                ],
            ),
        ]);
    }

    public function post(string $locale, string $slug): View
    {
        $translation = BlogPostTranslation::query()
            ->where('locale', $locale)
            ->where('slug', $slug)
            ->whereHas('post', fn ($query) => $query
                ->where('is_published', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now()))
            ->with(['post.translations', 'post.author'])
            ->firstOrFail();

        $post = $translation->post;
        $alternates = $post->translations
            ->mapWithKeys(fn ($item) => [$item->locale => route('blog.show', ['locale' => $item->locale, 'slug' => $item->slug])])
            ->all();

        $related = BlogPost::query()
            ->whereKeyNot($post->id)
            ->where('is_published', true)
            ->where('published_at', '<=', now())
            ->whereHas('translations', fn ($query) => $query->where('locale', $locale))
            ->with('translations')
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('content.blog-show', [
            'translation' => $translation,
            'related' => $related,
            'seo' => new SeoData(
                $translation->seo_title ?: $translation->title.' | KabulFit',
                $translation->seo_description ?: ($translation->excerpt ?? ''),
                route('blog.show', ['locale' => $locale, 'slug' => $translation->slug]),
                $alternates,
                'index,follow',
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'BlogPosting',
                    'headline' => $translation->title,
                    'description' => $translation->seo_description ?: $translation->excerpt,
                    'datePublished' => $post->published_at?->toIso8601String(),
                    'dateModified' => $post->updated_at?->toIso8601String(),
                    'mainEntityOfPage' => route('blog.show', ['locale' => $locale, 'slug' => $translation->slug]),
                    'publisher' => ['@type' => 'Organization', 'name' => 'KabulFit'],
                ],
            ),
        ]);
    }
}
