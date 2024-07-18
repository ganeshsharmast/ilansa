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

    'paths' => ['api/*', //'sanctum/csrf-cookie'
    ],

    'allowed_methods' => ['*','GET', 'POST', 'OPTIONS','methods'],

    'allowed_origins' => ['http://localhost:8100','http://localhost','https://localhost:8100','https://localhost','https://ilansa.shailtech.com','capacitor://localhost','*'],

    //'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*','Content-Type','headers'],

    'exposed_headers' => ['headers'],

    'max_age' => 0,

    'supports_credentials' => false,

];
