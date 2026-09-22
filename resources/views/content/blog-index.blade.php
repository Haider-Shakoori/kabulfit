@extends('layouts.app')

@section('content')
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">{{ __('content.blog') }}</p>
        <h1>{{ __('content.blog_title') }}</h1>
        <p>{{ __('content.blog_description') }}</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="product-grid">
            @forelse($posts as $post)
                @php($t=$post->translation(app()->getLocale()))
                <article class="account-panel">
                    <p class="muted">{{ __('content.published') }} {{ $post->published_at?->format('Y-m-d') }}</p>
                    <h2>{{ $t?->title }}</h2>
                    <p>{{ $t?->excerpt }}</p>
                    <a class="button button-secondary" href="{{ route('blog.show',['locale'=>app()->getLocale(),'slug'=>$t?->slug]) }}">{{ __('content.read_article') }}</a>
                </article>
            @empty
                <article class="account-panel"><p>{{ __('content.blog_description') }}</p></article>
            @endforelse
        </div>
        {{ $posts->links() }}
    </div>
</section>
@endsection
