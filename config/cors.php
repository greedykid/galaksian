<?php

$env = env('APP_ENV', 'production');

$devOrigins = in_array($env, ['local', 'testing'], true) ? [
    'http://localhost:3000',
    'http://localhost:5173',
    'http://127.0.0.1:8000',
] : [];

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Origin diizinkan berapa pun harus dikonfigurasi eksplisit via env
    | FRONTEND_URL / APP_URL. Origin localhost dev hanya aktif di local/testing.
    | API ini memakai bearer token (bukan cookie), sehingga supports_credentials
    | tetap false — ini lebih aman.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_values(array_unique(array_filter([
        env('FRONTEND_URL'),
        env('APP_URL'),
        ...$devOrigins,
    ]))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
