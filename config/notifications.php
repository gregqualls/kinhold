<?php

/**
 * Kinhold notification type registry.
 *
 * Adding a new push/email notification type:
 * 1. Drop a class in app/Notifications/ that uses User::wants($channel, $key) inside via().
 * 2. Add an entry to 'types' below — the Settings UI and User::wants() pick it up automatically.
 * 3. Wire the dispatch site (controller, service, scheduled command, etc).
 *
 * Categories drive the grouped accordion in the Settings UI. Adding a new category
 * is just a new key here plus a translatable label.
 */
return [

    'categories' => [
        'tasks' => 'Tasks',
        'points' => 'Points & Kudos',
        'shopping' => 'Shopping',
        'calendar' => 'Calendar',
        'food' => 'Food & Meals',
        'family' => 'Family activity',
        'billing' => 'Billing & subscription',
    ],

    /*
     * Each type entry:
     *   - category:        which group it appears under in Settings
     *   - label:           one-line user-facing description ("When ...")
     *   - description:     optional secondary line (sub-text under the toggle row)
     *   - channels:        which delivery channels are SUPPORTED (controls which switches render)
     *   - default_email:   on/off default for new users (only meaningful if 'email' in channels)
     *   - default_push:    on/off default for new users (only meaningful if 'push' in channels)
     *   - requires_module: optional Family module key ('tasks', 'points', etc) — type is hidden
     *                      from Settings if the family has the module disabled
     */
    'types' => [

        'task_assigned' => [
            'category' => 'tasks',
            'label' => 'When someone assigns me a task',
            'channels' => ['email', 'push'],
            'default_email' => true,
            'default_push' => true,
            'requires_module' => 'tasks',
        ],

        'task_completed' => [
            'category' => 'tasks',
            'label' => 'When a task I created is completed',
            'channels' => ['email'],
            'default_email' => true,
            'default_push' => false,
            'requires_module' => 'tasks',
        ],

        'kudos_received' => [
            'category' => 'points',
            'label' => 'When a family member gives me kudos',
            'channels' => ['email', 'push'],
            'default_email' => false,
            'default_push' => true,
            'requires_module' => 'points',
        ],

        'weekly_digest' => [
            'category' => 'family',
            'label' => 'Weekly digest',
            'description' => 'Sunday morning summary of your week',
            'channels' => ['email'],
            'default_email' => true,
            'default_push' => false,
        ],

        'family_invite' => [
            'category' => 'family',
            'label' => 'Family invitations',
            'channels' => ['email'],
            'default_email' => true,
            'default_push' => false,
        ],

        'task_due_soon' => [
            'category' => 'tasks',
            'label' => 'When a task I own is due today',
            'description' => 'A reminder at 8am the day a task is due',
            'channels' => ['email', 'push'],
            'default_email' => false,
            'default_push' => true,
            'requires_module' => 'tasks',
        ],

        'shopping_item_added' => [
            'category' => 'shopping',
            'label' => 'When someone adds to a shared shopping list',
            'channels' => ['push'],
            'default_email' => false,
            'default_push' => false,
            'requires_module' => 'shopping',
        ],

        'calendar_event_reminder' => [
            'category' => 'calendar',
            'label' => 'Reminders before calendar events',
            'description' => 'Set the lead time per event when creating it',
            'channels' => ['email', 'push'],
            'default_email' => false,
            'default_push' => true,
        ],

        'dinner_reminder' => [
            'category' => 'food',
            'label' => "What's for dinner today",
            'description' => "A daily push with tonight's planned meal",
            'channels' => ['push'],
            'default_email' => false,
            'default_push' => false,
            'requires_module' => 'food',
        ],

        // Billing lifecycle — sent only to the family's billing owner. These are
        // transactional (failed payments, cancellations, downgrades), so default
        // is on and most users won't toggle this. Opt-out remains possible.
        'billing' => [
            'category' => 'billing',
            'label' => 'Billing & subscription notices',
            'description' => 'Failed payments, trial endings, cancellations',
            'channels' => ['email'],
            'default_email' => true,
            'default_push' => false,
        ],

    ],

];
