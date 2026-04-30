<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Hosting Mode
    |--------------------------------------------------------------------------
    | Mirrors SELF_HOSTED env var so tests + service code read it through
    | config() and can override deterministically. Self-hosted instances
    | bypass AI usage limits entirely (their key, their cost).
    */
    'self_hosted' => (bool) env('SELF_HOSTED', false),

    /*
    |--------------------------------------------------------------------------
    | Commercial License Acknowledged
    |--------------------------------------------------------------------------
    | Internal flag — not advertised in .env.example or the SPA. Operators who
    | obtain a commercial license from the Kinhold project owner are given
    | instructions privately to set this to true; that suppresses the
    | single-family warning banner. The flag asserts the operator has a
    | license; it does not grant one.
    */
    'commercial_license_acknowledged' => (bool) env('COMMERCIAL_LICENSE_ACKNOWLEDGED', false),

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
        'model' => env('CHATBOT_MODEL', 'claude-haiku-4-5-20251001'),
        'enabled' => (bool) env('ANTHROPIC_API_KEY'),

        // Plan registry. Adding a tier or tweaking a number is a one-line edit
        // here — no schema change. Stripe price IDs are reserved slots that
        // stay null until #70 lands; the webhook will write the slug into
        // families.settings.chatbot.plan and the limit code reads it from here.
        'plans' => [
            'free' => [
                'name' => 'Free',
                'daily_messages' => 25,
                'price_monthly_cents' => 0,
                'stripe_price_id' => null,
                'public' => false,
            ],
            'lite' => [
                'name' => 'AI Lite',
                'daily_messages' => 50,
                'price_monthly_cents' => 500,
                'stripe_price_id' => env('STRIPE_PRICE_AI_LITE'),
                'public' => true,
            ],
            'standard' => [
                'name' => 'AI Standard',
                'daily_messages' => 150,
                'price_monthly_cents' => 1500,
                'stripe_price_id' => env('STRIPE_PRICE_AI_STANDARD'),
                'public' => true,
            ],
            'pro' => [
                'name' => 'AI Pro',
                'daily_messages' => 300,
                'price_monthly_cents' => 3000,
                'stripe_price_id' => env('STRIPE_PRICE_AI_PRO'),
                'public' => true,
            ],
        ],
        'default_plan' => env('CHATBOT_DEFAULT_PLAN', 'free'),
        'demo_plan' => env('CHATBOT_DEMO_PLAN', 'lite'),
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
