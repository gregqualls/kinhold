# Session Handoff

**Date:** 2026-04-27
**Branch:** `feature/demo-page` (PR #177 open)
**Last commit:** `d4dbbfc fix: also gate chrome on route.name to fully eliminate boot flash`

## What Was Done This Session

- **Reviewed v1.0.0 P0 issues** — all four shipped with v1.4.0 (April 17). Audit redirected session to Phase A: landing/SPA split.
- **Removed landing page from SPA** (#134) — `LandingView.vue` + 5 screenshot PNGs deleted. `/` redirects to `/login`. PR #176 merged.
- **Created `/demo` landing page** — full-page demo member picker reusing `authStore.demoLogin()`. Replaces the modal so marketing site can deep-link to `/demo` instead of `/login`.
- **Fixed SPA boot flash** — dashboard chrome briefly rendered on `/login` and `/demo` during initial auth check. Gated chrome visibility on both `initialAuthChecked` and `route.name` being set.
- **Protected main branch** — force-push and deletion blocked via GitHub.

## Quality State

- **Tests:** 125/125 pass ✅
- **Pint:** Unchanged (pre-existing Windows CRLF issues)
- **PHPStan:** No errors ✅
- **ESLint:** 0 errors, 2019 pre-existing warnings ✅
- **Vite build:** Pass ✅
- **CI (PR #177):** All checks green ✅

## What's Next

1. **Merge PR #177** — ships `/demo` page + boot-flash fix to production.
2. **Phase A: GDPR data export (#96)** — account deletion done, data export pending. High priority.
3. **Phase F Step 8: MCP tools for Food** — 3 issues remain in Phase F.

## Blockers or Gotchas

- **Upsun CLI auth expired** — use `upsun auth:browser-login` if you need to check deployment status manually.
- **Local dev on SQLite** — `.env.bak.pgsql` is gitignored. Production uses Postgres.

## Open Questions

None — ready to ship.
