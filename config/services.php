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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        'fallback_models' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('GEMINI_FALLBACK_MODELS', 'gemini-2.5-flash-lite,gemini-1.5-flash'))
        ))),
        'timeout_seconds' => (int) env('GEMINI_TIMEOUT_SECONDS', 120),
        'connect_timeout_seconds' => (int) env('GEMINI_CONNECT_TIMEOUT_SECONDS', 15),
        'retry_times' => (int) env('GEMINI_RETRY_TIMES', 2),
        'retry_sleep_ms' => (int) env('GEMINI_RETRY_SLEEP_MS', 1500),
        'max_output_tokens' => (int) env('GEMINI_MAX_OUTPUT_TOKENS', 4096),
    ],

];
