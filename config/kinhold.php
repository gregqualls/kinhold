<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Vault Configuration
    |--------------------------------------------------------------------------
    */
    'vault' => [
        'encryption_key' => env('VAULT_ENCRYPTION_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Calendar Configuration
    |--------------------------------------------------------------------------
    */
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Chatbot Configuration
    |--------------------------------------------------------------------------
    */
    'chatbot' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'model' => env('CHATBOT_MODEL', 'claude-sonnet-4-5-20250929'),
        'enabled' => (bool) env('ANTHROPIC_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Family Configuration
    |--------------------------------------------------------------------------
    */
    'family' => [
        'max_members' => env('FAMILY_MAX_MEMBERS', 20),
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Modules
    |--------------------------------------------------------------------------
    */
    'modules' => [
        'calendar' => (bool) env('MODULE_CALENDAR', true),
        'tasks' => (bool) env('MODULE_TASKS', true),
        'vault' => (bool) env('MODULE_VAULT', true),
        'chat' => (bool) env('MODULE_CHAT', true),
    ],
];
