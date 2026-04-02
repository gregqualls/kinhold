# Session Handoff

**Date:** 2026-04-02
**Branch:** feature/calendar-unified-events (PR #127)
**Last commit:** 0be3209 fix: countdown banner dismiss persistence — handle async prop arrival

## What Was Done This Session
- **Unified event model** — Merged `FeaturedEvent` and `FamilyEvent` into single `family_events` table. Any calendar event can be "featured" on the dashboard with personal or family scope. Migration copies existing data.
- **Manual calendar events** — Full CRUD from calendar UI with recurrence, visibility (visible/busy/private), and featured-on-dashboard toggle. Click-a-day to create, click-to-edit.
- **Countdown banner fixes** — Dismiss persists in localStorage (fixed async prop race condition), auto-hides past events, parent management actions from banner. Also fixed countdown toggle race condition in `setCountdown`.
- **MCP parity** — ViewCalendar: fixed empty listing when no Google connections, added create/update/delete. ManageFeaturedEvents repointed to unified model.
- **Security hardening** — Policy-based auth on CRUD, parent-only guards on featured fields, ownership checks on MCP update/delete.

## Quality State
- Tests: 60 tests, 118 assertions (pass, 2 deprecations)
- Pint: pass
- Larastan: pass (0 new errors, 218 baselined)
- ESLint: pass (0 errors, 43 warnings — all pre-existing)
- Build: pass (3175 modules, 2.42s)
- CI: all 3 checks green on PR #127

## What's Next
1. **Merge PR #127** — QA passed locally. Upsun preview has auth config issues (see Blockers). Greg should test on preview or merge and test on production.
2. **Drop `featured_events` table** — Data migrated to `family_events`. Follow-up migration to drop old table after verifying production.
3. **Fix Upsun preview auth** — Add preview domain pattern to SANCTUM_STATEFUL_DOMAINS. Blocks QA on previews for all future PRs.
4. **Continue from ROADMAP** — #65 Shopping lists (Phase A) or #117 Versioning/releases.

## Blockers or Gotchas
- **Upsun preview auth broken** — Preview domains aren't in `SANCTUM_STATEFUL_DOMAINS`, so API calls return 401. Google OAuth auto-redirects fail too (preview domain not in Google Cloud Console). Pre-existing but became visible during QA.
- **`FeaturedEvent` model still exists** — Kept for rollback safety. Don't use in new code. Remove after verifying production migration.
- **lodash-es npm audit high** — Transitive dep from Milkdown, no newer version available. Browser-bundled, not a real risk.
- Pint formats differently on PHP 8.4 (CI) vs PHP 8.5 (local) — watch for `array_indentation` issues.

## Open Questions
- How to handle Upsun preview auth — env var wildcard for SANCTUM_STATEFUL_DOMAINS, or Laravel config change?
- When to drop the `featured_events` table — next session after verifying production, or wait longer?
