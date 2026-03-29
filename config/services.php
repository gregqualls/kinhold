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
        'key' => env('RESEND_API_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_AUTH_REDIRECT_URI', '/auth/google/callback'),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Provider Defaults
    |--------------------------------------------------------------------------
    |
    | Default model names for each supported AI provider. These are used when
    | the family has not set a model override in their settings.
    |
    */
    'ai_providers' => [
        'anthropic' => [
            'default_model' => env('AI_ANTHROPIC_MODEL', 'claude-3-5-sonnet-20241022'),
        ],
        'openai' => [
            'default_model' => env('AI_OPENAI_MODEL', 'gpt-4o'),
        ],
        'google' => [
            'default_model' => env('AI_GOOGLE_MODEL', 'gemini-2.0-flash'),
        ],
    ],

];
