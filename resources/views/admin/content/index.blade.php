@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <x-form-errors />
    @if(session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Content & Journal</h1>
        <p class="mt-1 text-sm text-gray-500">Manage localized pages, SEO, and journal posts.</p>
    </div>

    <section class="mb-8">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Pages</h2>
            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-gray-500 shadow-sm">{{ $pages->count() }}</span>
        </div>
        <div class="space-y-4">
            @foreach($pages as $page)
                @php($translations=$page->translations->keyBy('locale'))
                <details class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-5"><div><p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#8B1538]">Page</p><h3 class="mt-1 font-semibold text-gray-900">{{ $page->page_key }}</h3></div><span class="grid h-10 w-10 place-items-center rounded-lg bg-gray-100 transition group-open:rotate-180">⌄</span></summary>
                    <form method="POST" action="{{ route('admin.content.pages.update', ['locale' => app()->getLocale(), 'page' => $page]) }}" class="border-t border-gray-100 p-5">
                        @csrf @method('PUT')
                        <label class="mb-5 flex items-center gap-3 rounded-lg bg-gray-50 px-4 py-3"><input name="is_published" type="checkbox" value="1" @checked($page->is_published) class="h-4 w-4 rounded text-[#8B1538]"><span class="text-sm font-medium">Published</span></label>
                        <div class="grid gap-4">
                            @foreach(config('kabulfit.supported_locales') as $loc)
                                @php($t=$translations->get($loc))
                                <fieldset class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                                    <legend class="px-2 text-xs font-bold uppercase tracking-[0.15em] text-[#8B1538]">{{ strtoupper($loc) }}</legend>
                                    <div class="grid gap-4 md:grid-cols-2">
                                        <label class="grid gap-1.5"><span class="text-sm font-medium">Title</span><input name="translations[{{ $loc }}][title]" value="{{ $t?->title }}" required class="rounded-md border border-gray-200 bg-white px-3 py-2.5"></label>
                                        <label class="grid gap-1.5"><span class="text-sm font-medium">Slug</span><input name="translations[{{ $loc }}][slug]" value="{{ $t?->slug }}" required class="rounded-md border border-gray-200 bg-white px-3 py-2.5"></label>
                                        <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">Excerpt</span><textarea name="translations[{{ $loc }}][excerpt]" rows="2" class="rounded-md border border-gray-200 bg-white px-3 py-2.5">{{ $t?->excerpt }}</textarea></label>
                                        <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">Body</span><textarea name="translations[{{ $loc }}][body]" rows="8" required class="rounded-md border border-gray-200 bg-white px-3 py-2.5">{{ $t?->body }}</textarea></label>
                                        <label class="grid gap-1.5"><span class="text-sm font-medium">SEO title</span><input name="translations[{{ $loc }}][seo_title]" value="{{ $t?->seo_title }}" class="rounded-md border border-gray-200 bg-white px-3 py-2.5"></label>
                                        <label class="grid gap-1.5"><span class="text-sm font-medium">SEO description</span><textarea name="translations[{{ $loc }}][seo_description]" rows="2" class="rounded-md border border-gray-200 bg-white px-3 py-2.5">{{ $t?->seo_description }}</textarea></label>
                                    </div>
                                </fieldset>
                            @endforeach
                        </div>
                        <div class="mt-5 flex justify-end"><button type="submit" class="rounded-md bg-[#8B1538] px-5 py-2.5 text-sm font-semibold text-white">Save page</button></div>
                    </form>
                </details>
            @endforeach
        </div>
    </section>

    <section>
        <div class="mb-4 flex items-center justify-between"><h2 class="text-lg font-semibold text-gray-900">Journal</h2></div>

        <details class="group mb-4 overflow-hidden rounded-xl border border-dashed border-gray-300 bg-white shadow-sm">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-5"><div class="flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-lg bg-[#8B1538] text-white"><x-icon name="plus" class="h-5 w-5" /></span><strong>Create journal post</strong></div><span class="transition group-open:rotate-180">⌄</span></summary>
            <form method="POST" action="{{ route('admin.content.posts.store', ['locale' => app()->getLocale()]) }}" class="border-t border-gray-100 p-5">
                @csrf
                <label class="mb-5 flex items-center gap-3 rounded-lg bg-gray-50 px-4 py-3"><input name="is_published" type="checkbox" value="1" class="h-4 w-4 rounded text-[#8B1538]"><span class="text-sm font-medium">Publish immediately</span></label>
                <div class="grid gap-4">
                    @foreach(config('kabulfit.supported_locales') as $loc)
                        <fieldset class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                            <legend class="px-2 text-xs font-bold uppercase tracking-[0.15em] text-[#8B1538]">{{ strtoupper($loc) }}</legend>
                            <div class="grid gap-4 md:grid-cols-2">
                                <label class="grid gap-1.5"><span class="text-sm font-medium">Title</span><input name="translations[{{ $loc }}][title]" required class="rounded-md border border-gray-200 bg-white px-3 py-2.5"></label>
                                <label class="grid gap-1.5"><span class="text-sm font-medium">Slug</span><input name="translations[{{ $loc }}][slug]" required class="rounded-md border border-gray-200 bg-white px-3 py-2.5"></label>
                                <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">Excerpt</span><textarea name="translations[{{ $loc }}][excerpt]" rows="2" class="rounded-md border border-gray-200 bg-white px-3 py-2.5"></textarea></label>
                                <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">Body</span><textarea name="translations[{{ $loc }}][body]" rows="8" required class="rounded-md border border-gray-200 bg-white px-3 py-2.5"></textarea></label>
                                <label class="grid gap-1.5"><span class="text-sm font-medium">SEO title</span><input name="translations[{{ $loc }}][seo_title]" class="rounded-md border border-gray-200 bg-white px-3 py-2.5"></label>
                                <label class="grid gap-1.5"><span class="text-sm font-medium">SEO description</span><textarea name="translations[{{ $loc }}][seo_description]" rows="2" class="rounded-md border border-gray-200 bg-white px-3 py-2.5"></textarea></label>
                            </div>
                        </fieldset>
                    @endforeach
                </div>
                <div class="mt-5 flex justify-end"><button type="submit" class="rounded-md bg-[#8B1538] px-5 py-2.5 text-sm font-semibold text-white">Create post</button></div>
            </form>
        </details>

        <div class="space-y-4">
            @foreach($posts as $post)
                @php($translations=$post->translations->keyBy('locale'))
                <details class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-5"><div><p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#2A6867]">Post</p><h3 class="mt-1 font-semibold text-gray-900">{{ $translations->get('en')?->title ?? $post->uuid }}</h3></div><span class="transition group-open:rotate-180">⌄</span></summary>
                    <form method="POST" action="{{ route('admin.content.posts.update', ['locale' => app()->getLocale(), 'post' => $post]) }}" class="border-t border-gray-100 p-5">
                        @csrf @method('PUT')
                        <label class="mb-5 flex items-center gap-3 rounded-lg bg-gray-50 px-4 py-3"><input name="is_published" type="checkbox" value="1" @checked($post->is_published) class="h-4 w-4 rounded text-[#8B1538]"><span class="text-sm font-medium">Published</span></label>
                        @foreach(config('kabulfit.supported_locales') as $loc)
                            @php($t=$translations->get($loc))
                            <fieldset class="mb-4 rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                                <legend class="px-2 text-xs font-bold uppercase tracking-[0.15em] text-[#8B1538]">{{ strtoupper($loc) }}</legend>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <label class="grid gap-1.5"><span class="text-sm font-medium">Title</span><input name="translations[{{ $loc }}][title]" value="{{ $t?->title }}" required class="rounded-md border border-gray-200 bg-white px-3 py-2.5"></label>
                                    <label class="grid gap-1.5"><span class="text-sm font-medium">Slug</span><input name="translations[{{ $loc }}][slug]" value="{{ $t?->slug }}" required class="rounded-md border border-gray-200 bg-white px-3 py-2.5"></label>
                                    <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">Excerpt</span><textarea name="translations[{{ $loc }}][excerpt]" rows="2" class="rounded-md border border-gray-200 bg-white px-3 py-2.5">{{ $t?->excerpt }}</textarea></label>
                                    <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">Body</span><textarea name="translations[{{ $loc }}][body]" rows="8" required class="rounded-md border border-gray-200 bg-white px-3 py-2.5">{{ $t?->body }}</textarea></label>
                                    <label class="grid gap-1.5"><span class="text-sm font-medium">SEO title</span><input name="translations[{{ $loc }}][seo_title]" value="{{ $t?->seo_title }}" class="rounded-md border border-gray-200 bg-white px-3 py-2.5"></label>
                                    <label class="grid gap-1.5"><span class="text-sm font-medium">SEO description</span><textarea name="translations[{{ $loc }}][seo_description]" rows="2" class="rounded-md border border-gray-200 bg-white px-3 py-2.5">{{ $t?->seo_description }}</textarea></label>
                                </div>
                            </fieldset>
                        @endforeach
                        <div class="flex flex-wrap gap-3">
                            <button type="submit" class="rounded-md bg-[#8B1538] px-5 py-2.5 text-sm font-semibold text-white">Save post</button>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('admin.content.posts.destroy', ['locale' => app()->getLocale(), 'post' => $post]) }}" class="border-t border-gray-100 p-5">
                        @csrf @method('DELETE')
                        <button type="submit" class="rounded-md border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">Delete post</button>
                    </form>
                </details>
            @endforeach
        </div>
    </section>
</div>
@endsection
