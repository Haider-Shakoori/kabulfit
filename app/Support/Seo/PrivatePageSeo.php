<?php

namespace App\Support\Seo;

final class PrivatePageSeo
{
    public static function make(string $title, string $canonical): SeoData
    {
        return new SeoData(
            title: $title.' | KabulFit',
            description: '',
            canonical: $canonical,
            robots: 'noindex,nofollow',
        );
    }
}
