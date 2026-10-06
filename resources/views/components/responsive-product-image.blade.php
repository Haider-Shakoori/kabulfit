@props([
    'media',
    'alt' => '',
    'loading' => 'lazy',
    'fetchpriority' => null,
    'sizes' => '(max-width: 768px) 100vw, 33vw',
])

@php
    $sources = $media->responsiveSources();
    $fallbackWebp = '';

    if (empty($sources['webp']) && str_starts_with($media->path, 'images/kabulfit-live/catalog/product-')) {
        $base = pathinfo($media->path, PATHINFO_FILENAME);
        $candidate320 = 'images/kabulfit-optimized/catalog/'.$base.'-320.webp';
        $candidate640 = 'images/kabulfit-optimized/catalog/'.$base.'-640.webp';
        $fallback = [];

        if (is_file(public_path($candidate320))) {
            $fallback[] = asset($candidate320).' 320w';
        }

        if (is_file(public_path($candidate640))) {
            $fallback[] = asset($candidate640).' 640w';
        }

        $fallbackWebp = implode(', ', $fallback);
    }
@endphp
<picture>
    @if (! empty($sources['avif']))
        <source type="image/avif" srcset="{{ $sources['avif'] }}" sizes="{{ $sizes }}">
    @endif
    @if (! empty($sources['webp']) || $fallbackWebp !== '')
        <source type="image/webp" srcset="{{ $sources['webp'] ?: $fallbackWebp }}" sizes="{{ $sizes }}">
    @endif
    <img
        {{ $attributes }}
        src="{{ $media->url() }}"
        width="{{ $media->width }}"
        height="{{ $media->height }}"
        alt="{{ $alt }}"
        loading="{{ $loading }}"
        decoding="async"
        @if($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
    >
</picture>
