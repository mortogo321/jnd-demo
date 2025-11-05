<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter(array_merge(
        // Parse domain from APP_URL
        [parse_url(env('APP_URL', 'http://localhost'), PHP_URL_SCHEME).'://'.parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST).(parse_url(env('APP_URL', 'http://localhost'), PHP_URL_PORT) ? ':'.parse_url(env('APP_URL', 'http://localhost'), PHP_URL_PORT) : '')],
        // Additional origins from env
        explode(',', env('CORS_ALLOWED_ORIGINS', ''))
    )),

    'allowed_origins_patterns' => [
        // Allow localhost/127.0.0.1 on any port for local development
        '/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?$/',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
