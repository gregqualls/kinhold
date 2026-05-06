# Session Handoff

**Date:** 2026-05-06
**Branch:** `staging` (production cutover deferred until Stripe bank account verified — later this week)
**Last commit:** `3506b38` — fix: do not cache authenticated API responses in the service worker

## What Was Done This Session

- **PR [#283](https://github.com/gregqualls/kinhold/pull/283)** — switched SW runtime cache for `/api/*` from `NetworkFirst` to `NetworkOnly`. The `NetworkFirst` strategy was caching `/api/v1/user` and causing returning visitors to auto-login as a previously-cached user (cross-session auth bleed + CSRF mismatches). Verified clean on staging.
- **Closed 24 stale issues** — all v1.9.0 work that had shipped to staging but wasn't closed in GitHub.
- **Cost analysis (no code changes):**
  - Hosting: $47/month fixed (Upsun Flexible, current prod resources)
  - AI: $0.0036/msg blended (Haiku 4.5, 70% cached turns)
  - Margins at typical utilization (18 active days, 40% cap): 78–85% across all tiers
  - Margins at max utilization: 17–64% — thin on Pro but still profitable because the $10 base plan offsets AI add-on losses
  - Recommendation: pricing is fine for launch. Watch for power Pro users; consider a monthly message cap later.
- **Staging paused** after DB queries to stop idle billing.

## Quality State

- Tests: 342 tests, 880 assertions — **PASS**
- Pint: **PASS**
- Larastan: 0 errors — **PASS**
- ESLint: 36 warnings, 0 errors — **PASS** (warnings are pre-existing `no-unused-vars`, not new)
- Build: PASS (4 precache entries, 819 KB)

## What's Next

1. **Production cutover** — once Greg's Stripe bank account is verified (later this week), open the staging→main PR. Checklist in `docs/V190-STAGING-PLAN.md`:
   - Switch Stripe to live mode (new product + prices, live `STRIPE_KEY`/`STRIPE_SECRET`/price IDs)
   - Register prod webhook in Stripe live mode (events listed in the staging plan doc)
   - Set Upsun prod env vars: `BILLING_ENABLED=true`, live Stripe keys + webhook secret + price IDs
   - Open staging→main PR (Upsun auto-deploys main to prod — never push directly)
   - Smoke test prod end-to-end before announcing
2. **AI pricing refinement** (low priority) — once 60 days of live token data is available, tune daily caps or add monthly caps if power users are running Pro at >70% utilization sustained.
3. **Phase A items** still open per ROADMAP: landing page split ([#134](https://github.com/gregqualls/kinhold/issues/134)), AI usage limits ([#137](https://github.com/gregqualls/kinhold/issues/137)), self-host single-family enforcement ([#138](https://github.com/gregqualls/kinhold/issues/138)).

## Blockers or Gotchas

- Staging is **paused** — resume with `upsun environment:resume --project 2rozcvqjtjdta --environment staging` before any staging work.
- Stripe is still in **test/sandbox mode** on prod. `BILLING_ENABLED` is false on prod, so all Stripe code paths are safely gated. Do not flip `BILLING_ENABLED=true` until live Stripe keys are in place.
- The staging→main PR was authorized by Greg but blocked by sandbox in the previous session (sandbox cited prior "main is production" session context). Re-attempt once bank account is confirmed.

## Open Questions

- When exactly is the Stripe bank account verification expected? (Determines cutover timing.)
- Should a monthly message cap be added to Pro/Standard now (e.g., Standard: 2,500/month, Pro: 6,000/month) as a guard before launch, or wait for real usage data first?
