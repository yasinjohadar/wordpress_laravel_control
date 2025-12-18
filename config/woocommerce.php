<?php

return [
    'store_url' => env('WOOCOMMERCE_STORE_URL'),
    'consumer_key' => env('WOOCOMMERCE_CONSUMER_KEY'),
    'consumer_secret' => env('WOOCOMMERCE_CONSUMER_SECRET'),
    'verify_ssl' => env('WOOCOMMERCE_VERIFY_SSL', true),
    'api_version' => env('WOOCOMMERCE_API_VERSION', 'wc/v3'),
    'timeout' => env('WOOCOMMERCE_TIMEOUT', 30),
    'webhook_secret' => env('WOOCOMMERCE_WEBHOOK_SECRET'),
    
    // Cache settings
    'cache' => [
        'enabled' => env('WOOCOMMERCE_CACHE_ENABLED', false),
        'ttl' => env('WOOCOMMERCE_CACHE_TTL', 300), // 5 minutes
    ],
    
    // Logging settings
    'logging' => [
        'enabled' => env('WOOCOMMERCE_LOGGING_ENABLED', false),
        'channel' => env('WOOCOMMERCE_LOG_CHANNEL', 'stack'),
    ],
];

