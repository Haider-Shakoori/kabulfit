@extends('layouts.app')

@section('content')
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">{{ __('content.blog') }}</p>
        <h1>{{ $translation->title }}</h1>
        <p>{{ $translation->excerpt }}</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <article class="account-panel">
            <p class="muted">{{ __('content.published') }} {{ $translation->post->published_at?->format('Y-m-d') }}</p>
            @foreach(preg_split('/\R{2,}/u', trim($translation->body)) as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </article>

        @if($related->isNotEmpty())
            <section class="account-panel">
                <h2>{{ __('content.related_articles') }}</h2>
                <div class="address-list">
                    @foreach($related as $post)
                        @php($t=$post->translation(app()->getLocale()))
                        <a href="{{ route('blog.show',['locale'=>app()->getLocale(),'slug'=>$t?->slug]) }}">{{ $t?->title }}</a>
                    @endforeach
                </div>
            </section>
        @endif

        <a class="button button-secondary" href="{{ route('blog.index',['locale'=>app()->getLocale()]) }}">{{ __('content.back_to_blog') }}</a>
    </div>
</section>
@endsection
