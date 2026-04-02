# Session Handoff

**Date:** 2026-04-02
**Branch:** feature/try-the-demo (PR #129, pending merge)
**Last commit:** 8b50090 feat: one-click "Try the Demo" with family member picker

## What Was Done This Session
- **Fresh demo family** — Demo users skip onboarding and email verification. Daily `app:refresh-demo` command re-seeds at 03:05. (PR #128, merged)
- **Hardened demo passwords** — `Str::random(32)` per seed run instead of `bcrypt('password')`. Changes daily.
- **"Try the Demo" feature** — Interactive modal on landing page + login page lets visitors pick a family member and instantly log in. `POST /api/v1/demo-login` creates Sanctum tokens directly.
- **Self-hosted compatible** — `demo_available` flag in `/api/v1/config` hides demo buttons when no demo family exists.
- **ESLint cleanup** — Eliminated all 43 pre-existing warnings across 23 files (PR #128, merged).

## Quality State
- Tests: 60 tests, 118 assertions (pass, 2 deprecations)
- Pint: pass
- ESLint: pass (0 errors, 0 warnings)
- Build: pass (Vite build clean, chunk size warning only)
- CI: running on PR #129

## What's Next
1. **Merge PR #129** — CI should be green, ready for `/merge`
2. **Audit all controllers for family_id scoping** — Critical before Corey's family signs up
3. **Shopping & grocery lists (issue #65)** — Phase A priority, #1 daily-driver feature
4. **PWA support (issue #68)** — Get the app installable on phones

## Blockers or Gotchas
- Composer on this machine is at `/usr/local/bin/composer` (not in default PATH), PHP at `/opt/homebrew/bin/php`
- **Upsun preview auth still broken** — Preview domains not in `SANCTUM_STATEFUL_DOMAINS`. Pre-existing, blocks QA on previews.
- NPM audit shows 1 high severity vuln in `lodash-es` (pre-existing)

## Open Questions
- None from this session
