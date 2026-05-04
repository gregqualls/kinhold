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
    | Billing (Stripe / Cashier) — see #70 umbrella
    |--------------------------------------------------------------------------
    | `billing_enabled` gates every billing surface in the SPA + API. Self-hosted
    | instances default to false (free forever under EL2). Hosted Kinhold flips
    | this to true once #70-A through #70-H have shipped (target: v1.9.0).
    |
    | Stripe Product/Price IDs follow the same pattern as `chatbot.plans.*`
    | (env-resolved, null until configured). 70-D moves the AI tier price IDs
    | from chatbot.plans into billing.ai.* — out of scope for 70-A.
    */
    'billing_enabled' => (bool) env('BILLING_ENABLED', false),

    'billing' => [
        'base_plan' => [
            'stripe_price_id' => env('STRIPE_PRICE_BASE_PLAN'),
            'price_monthly_cents' => 1000,
        ],
        'storage' => [
            'included_gb' => 5,
            'stripe_price_id' => env('STRIPE_PRICE_STORAGE_OVERAGE'),
            'overage_cents_per_gb' => 100,
        ],
        'trial_days' => (int) env('BILLING_TRIAL_DAYS', 14),
    ],

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
            // Demo tier (#266) — caps the shared marketing-demo family. Lower
            // than Lite because the demo is shared across every visitor and we
            // pay the API bill. Not user-selectable (`public` => false).
            'demo' => [
                'name' => 'Demo',
                'daily_messages' => (int) env('CHATBOT_DEMO_DAILY_MESSAGES', 10),
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
        'demo_plan' => env('CHATBOT_DEMO_PLAN', 'demo'),

        // Per-session cap for the shared demo family (#266). Even when the
        // family-wide daily cap has remaining capacity, a single visitor can
        // only send this many messages per browser session before being asked
        // to sign up.
        'demo_session_limit' => (int) env('CHATBOT_DEMO_SESSION_LIMIT', 3),

        // Monthly USD circuit-breaker for the shared demo family (#266). When
        // estimated month-to-date Anthropic spend exceeds this, demo AI is
        // disabled with a "demo is taking a breather" message until next month.
        'demo_monthly_cost_ceiling_cents' => (int) env('CHATBOT_DEMO_COST_CEILING_CENTS', 1000),

        // Pricing used to estimate the demo monthly spend. Per-million-token
        // rates from Anthropic Haiku 4.5. Update if the demo model changes.
        'demo_cost' => [
            'input_per_million_cents' => (int) env('CHATBOT_DEMO_COST_INPUT_PER_M_CENTS', 80),
            'output_per_million_cents' => (int) env('CHATBOT_DEMO_COST_OUTPUT_PER_M_CENTS', 400),
        ],
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
