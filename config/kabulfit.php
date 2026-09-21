<?php

return [
    'default_currency' => env('KABULFIT_DEFAULT_CURRENCY', 'AFN'),
    'default_locale' => env('KABULFIT_DEFAULT_LOCALE', 'en'),
    'supported_locales' => array_values(array_filter(explode(',', env('KABULFIT_SUPPORTED_LOCALES', 'en,fa,ps')))),
    'rtl_locales' => ['fa', 'ps'],
    'catalog' => [
        'per_page' => (int) env('KABULFIT_CATALOG_PER_PAGE', 12),
        'max_per_page' => 48,
    ],
];
