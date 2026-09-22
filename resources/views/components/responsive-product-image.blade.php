@props([
    'media',
    'alt' => '',
    'loading' => 'lazy',
    'fetchpriority' => null,
    'sizes' => '(max-width: 768px) 100vw, 33vw',
])

@php($sources = $media->responsiveSources())
<picture>
    @if (! empty($sources['avif']))
        <source type="image/avif" srcset="{{ $sources['avif'] }}" sizes="{{ $sizes }}">
    @endif
    @if (! empty($sources['webp']))
        <source type="image/webp" srcset="{{ $sources['webp'] }}" sizes="{{ $sizes }}">
    @endif
    <img
        src="{{ $media->url() }}"
        width="{{ $media->width }}"
        height="{{ $media->height }}"
        alt="{{ $alt }}"
        loading="{{ $loading }}"
        decoding="async"
        @if($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
    >
</picture>
