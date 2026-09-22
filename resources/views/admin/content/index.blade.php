@extends('layouts.app')
@section('content')
<section class="page-hero"><div class="container"><p class="eyebrow">Content & SEO</p><h1>Pages and journal</h1></div></section>
<section class="section"><div class="container account-grid">@include('admin._nav')
<div class="account-main"><x-form-errors />@if(session('status'))<div class="account-panel">{{ session('status') }}</div>@endif
@foreach($pages as $page)
@php($translations=$page->translations->keyBy('locale'))
<details class="account-panel"><summary><strong>Page:</strong> {{ $page->page_key }}</summary>
<form method="post" action="{{ route('admin.content.pages.update',['locale'=>app()->getLocale(),'page'=>$page]) }}" class="address-form">@csrf @method('PUT')
<label><input name="is_published" type="checkbox" value="1" @checked($page->is_published)> Published</label>
@foreach(config('kabulfit.supported_locales') as $locale)
@php($t=$translations->get($locale))
<fieldset><legend>{{ strtoupper($locale) }}</legend>
<label>Title<input name="translations[{{ $locale }}][title]" value="{{ $t?->title }}" required></label>
<label>Slug<input name="translations[{{ $locale }}][slug]" value="{{ $t?->slug }}" required></label>
<label>Excerpt<textarea name="translations[{{ $locale }}][excerpt]">{{ $t?->excerpt }}</textarea></label>
<label>Body<textarea name="translations[{{ $locale }}][body]" rows="8" required>{{ $t?->body }}</textarea></label>
<label>SEO title<input name="translations[{{ $locale }}][seo_title]" value="{{ $t?->seo_title }}"></label>
<label>SEO description<textarea name="translations[{{ $locale }}][seo_description]">{{ $t?->seo_description }}</textarea></label>
</fieldset>
@endforeach
<button class="button button-primary" type="submit">Save page</button></form></details>
@endforeach

<details class="account-panel"><summary><strong>Create journal post</strong></summary>
<form method="post" action="{{ route('admin.content.posts.store',['locale'=>app()->getLocale()]) }}" class="address-form">@csrf
<label><input name="is_published" type="checkbox" value="1"> Publish immediately</label>
@foreach(config('kabulfit.supported_locales') as $locale)
<fieldset><legend>{{ strtoupper($locale) }}</legend>
<label>Title<input name="translations[{{ $locale }}][title]" required></label>
<label>Slug<input name="translations[{{ $locale }}][slug]" required></label>
<label>Excerpt<textarea name="translations[{{ $locale }}][excerpt]"></textarea></label>
<label>Body<textarea name="translations[{{ $locale }}][body]" rows="8" required></textarea></label>
<label>SEO title<input name="translations[{{ $locale }}][seo_title]"></label>
<label>SEO description<textarea name="translations[{{ $locale }}][seo_description]"></textarea></label>
</fieldset>
@endforeach
<button class="button button-primary" type="submit">Create post</button></form></details>

@foreach($posts as $post)
@php($translations=$post->translations->keyBy('locale'))
<details class="account-panel"><summary><strong>Post:</strong> {{ $translations->get('en')?->title ?? $post->uuid }}</summary>
<form method="post" action="{{ route('admin.content.posts.update',['locale'=>app()->getLocale(),'post'=>$post]) }}" class="address-form">@csrf @method('PUT')
<label><input name="is_published" type="checkbox" value="1" @checked($post->is_published)> Published</label>
@foreach(config('kabulfit.supported_locales') as $locale)
@php($t=$translations->get($locale))
<fieldset><legend>{{ strtoupper($locale) }}</legend>
<label>Title<input name="translations[{{ $locale }}][title]" value="{{ $t?->title }}" required></label>
<label>Slug<input name="translations[{{ $locale }}][slug]" value="{{ $t?->slug }}" required></label>
<label>Excerpt<textarea name="translations[{{ $locale }}][excerpt]">{{ $t?->excerpt }}</textarea></label>
<label>Body<textarea name="translations[{{ $locale }}][body]" rows="8" required>{{ $t?->body }}</textarea></label>
<label>SEO title<input name="translations[{{ $locale }}][seo_title]" value="{{ $t?->seo_title }}"></label>
<label>SEO description<textarea name="translations[{{ $locale }}][seo_description]">{{ $t?->seo_description }}</textarea></label>
</fieldset>
@endforeach
<button class="button button-primary" type="submit">Save post</button></form>
<form method="post" action="{{ route('admin.content.posts.destroy',['locale'=>app()->getLocale(),'post'=>$post]) }}">@csrf @method('DELETE')<button class="button button-secondary" type="submit">Delete post</button></form>
</details>
@endforeach
</div></div></section>
@endsection
