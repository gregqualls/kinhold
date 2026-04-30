# Session Handoff — 2026-04-30

**Branch:** `feature/69a-web-push-foundation`
**PR:** [#208](https://github.com/gregqualls/kinhold/pull/208) — open, awaiting CI + review

## What Was Done This Session

Implemented **#69a — Web Push Notifications Foundation** (v1.8.0):

- `laravel-notification-channels/webpush ^10.5` (VAPID + WebPushChannel)
- `push_subscriptions` migration (bigIncrements PK, uuidMorphs UUID FK to users, unique endpoint)
- `notification_preferences` JSON column on users (migrated from `email_preferences`); quiet hours TZ-aware with overnight range support
- `config/notifications.php` — extensible type registry (category, label, channels, defaults, requires_module); Settings UI and `via()` are both driven from it
- `config/webpush.php` — published + pint-fixed
- `User::wants($channel, $key)` — unified preference check with registry fallback; `wantsEmail()` → thin wrapper
- `User::isPushSuppressed()` / `isPushMuted()` / `isInQuietHours()` — centralized suppression gates
- `TaskAssignedNotification` — gains WebPush channel, respects wants/suppress
- `KudosReceivedNotification` — new, dispatched from `PointsService::giveKudos()`
- `TestPushNotification` — bypasses pref gating, used by test endpoint
- `PushSubscriptionController` — store / destroy / test routes
- `SettingsController` — notification-preferences GET/PUT with registry-filtered shape
- `public/sw-push.js` — push + notificationclick handlers (loaded via workbox importScripts option)
- `resources/js/services/push.js` — subscribe/unsubscribe/register/test helpers
- `resources/js/stores/notifications.js` — Pinia store for prefs + push subscription lifecycle
- `resources/js/components/NotificationsPrompt.vue` — post-login permission banner (30-day cooldown)
- `resources/js/components/notifications/NotificationsPanel.vue` — full Settings UI (mute, quiet hours, category accordion with Email × Push grid)
- `SettingsView.vue` — Notifications sections replaced with `<NotificationsPanel />`
- `AccountDeletionService` — push subscriptions deleted on account deletion (GDPR)
- 20 new tests (Feature + Unit); 197/197 pass; PHPStan clean; ESLint clean; build clean

## Self-Hosted Setup (Required — documented in PR + SELF-HOSTING.md)

```bash
php artisan webpush:vapid
# Paste VAPID_PUBLIC_KEY, VAPID_PRIVATE_KEY, VAPID_SUBJECT into .env
php artisan config:clear && php artisan migrate
```

## What's Next

1. **CI + merge [PR #208](https://github.com/gregqualls/kinhold/pull/208).** Run `/qa` to grab Upsun preview URL. Real-device test: Android Chrome for subscribe flow, iOS Safari 16.4+ on installed PWA only.

2. **#69b — Scheduled reminders** (new branch `feature/69b-push-reminders`):
   - `TaskDueSoon` — scheduled command `*/30 * * * *`, `due_reminder_sent_at` dedup flag on tasks
   - `ShoppingListItemAdded` — fire on item add, exclude actor
   - `CalendarEventReminder` — `reminder_minutes_before` column on calendar_events, `*/5 * * * *` command
   - `DinnerReminder` — per-user `dinner_reminder_at` time (already in notification_preferences), reads today's meal plan dinner slot, `* * * * *` command

3. **Open non-blocking follow-ups from /review:**
   - N+1 fix: cache `pushSubscriptions()->count()` in `User::wants('push')` for scheduled commands
   - GDPR export: add `notification_preferences` to `UserDataExportService`
   - Disabled checkbox styling: add `disabled:opacity-40 disabled:cursor-not-allowed` in `NotificationsPanel.vue`

## Blockers / Gotchas

- **iOS push requires installed PWA.** Browser tab on iOS does not receive push events (Safari 16.4+ only, and only when app is installed via "Add to Home Screen"). This is documented in the Settings UI and SELF-HOSTING.md.
- **Pint CRLF noise** — pre-existing Windows line-ending warnings on push, not introduced this session.
- **VAPID keys required** — without `.env` VAPID vars the meta tag renders empty and the subscribe flow silently fails with a key parse error. Ensure keys are set on Upsun before smoke testing.

## Open Questions

None.
