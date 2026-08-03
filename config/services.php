<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SIEDA Backend Integration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk sinkronisasi user ke backend SIEDA (aplikasi PKK
    | di tingkat desa). SIEDA_SYNC_SECRET HARUS sama persis dengan nilai
    | di .env aplikasi SIEDA agar header X-Sieda-Key lolos middleware.
    |
    | Generate secret dengan: php artisan tinker --execute="echo Str::random(64);"
    |
    */

    'sieda' => [
        'base_url' => env('SIEDA_API_URL', 'http://127.0.0.1:8004'),
        'sync_secret' => env('SIEDA_SYNC_SECRET'),
    ],

];
