<?php

return [
    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'secret' => env('PAYPAL_SECRET'),
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        'base_url' => env('PAYPAL_MODE', 'sandbox') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com',
        'supported_currencies' => array_values(array_filter(array_map(
            fn (string $currency): string => strtoupper(trim($currency)),
            explode(',', (string) env('PAYPAL_SUPPORTED_CURRENCIES', 'USD')),
        ))),
    ],

    'meta' => [
        'pixel_id' => env('META_PIXEL_ID', '2203881733746506'),
    ],

    'analytics' => [
        'measurement_id' => env('GA_MEASUREMENT_ID'),
    ],
];
