<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Cookie-based auth (Sanctum SPA) requires:
    | - supports_credentials = true
    | - allowed_origins NOT '*'
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:5173,http://localhost:3000,http://127.0.0.1:5173,http://127.0.0.1:3000,http://127.0.0.1:8000,https://api-kedokteran-test.ubg.ac.id')))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
