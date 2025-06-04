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

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'openweather' => [
        'key' => env('OPENWEATHERMAP_API_KEY'),
        'weather_url' => env('OPENWEATHERMAP_WEATHER_URL', 'https://api.openweathermap.org/data/2.5/weather'),
        'geocoding_url' => env('OPENWEATHERMAP_GEOCODING_URL', 'https://api.openweathermap.org/geo/1.0'),
        'timeout' => env('OPENWEATHERMAP_TIMEOUT', 10),
        'language' => env('WEATHER_LANGUAGE', 'ru'),
        'city' => env('WEATHER_DEFAULT_CITY', 'Moscow'),
        'units' => env('WEATHER_UNITS', 'metric'),
        'forecast_cnt' => env('WEATHER_FORECAST_CNT', 8),
    ],

];
