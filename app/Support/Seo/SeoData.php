<?php

namespace App\Support\Seo;

final readonly class SeoData
{
    public function __construct(
        public string $title,
        public string $description,
        public string $canonical,
        public array $alternates = [],
        public string $robots = 'index,follow',
        public ?array $jsonLd = null,
    ) {
    }
}
