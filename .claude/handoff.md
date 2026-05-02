# Session Handoff

**Date:** 2026-05-02
**Branch:** `feature/216-storage-metering` (PR [#226](https://github.com/gregqualls/kinhold/pull/226) open, build green, awaiting `/qa` + `/merge`)
**Last commit:** `994a50a` — feat: billing storage metering — nightly tally + soft overage UI (70-C, #216)

## What Was Done This Session

- Implemented **70-C storage metering** ([#216](https://github.com/gregqualls/kinhold/issues/216)): `StorageMeteringService`, `family_storage_usages` table, `kinhold:tally-storage` artisan command (02:00 UTC), real-time recalc via `Document::booted()` hooks, polymorphic owner registry for future expansion
- `BillingService::summary()` returns a `storage.*` block; `createBaseCheckout()` includes the metered price on first subscribe
- `BillingPanel.vue` renders a usage bar with amber overage callout when over 5 GB; store deep-merges `storage` to preserve shape across cancel/resume
- Added 12 feature tests (delta-only Stripe push, polymorphic registry, overage math, billing-disabled gates) — all green
- Bumped to `v1.8.5`; CHANGELOG, ROADMAP, REFERENCE.md updated; PR opened with manual Stripe Dashboard setup steps in body

## Quality State

- **Tests:** 260 tests, 677 assertions — PASS (full suite); Billing slice: 32 tests, 78 assertions PASS
- **Pint:** FAIL on 6 files (`AuthController`, `BillingController`, `FamilyResource`, `routes/api.php`, two billing tests) — **all CRLF line-ending only** plus a few `unary_operator_spaces` fixes on files **not in 70-C scope** (70-A/B leftovers on main). Safe to ignore for #226; should be cleaned up in a follow-up Pint sweep.
- **PHPStan:** PASS (0 errors)
- **ESLint:** 0 errors, 36 warnings (all pre-existing — unused imports/vars in design-system + vault views)
- **Build:** PASS — 4 precache entries / 802.13 KiB

## What's Next

1. **`/qa` → `/merge` PR #226** once Upsun preview verifies. Then **manually configure Stripe sandbox** per PR body (create `kinhold_storage_gb` Meter, metered Price, archive old flat price, update `.env`). Stripe MCP doesn't expose Meter creation yet — must be done via Dashboard.
2. **70-D — AI tier purchase wiring** ([#217](https://github.com/gregqualls/kinhold/issues/217)). Next slice of the billing epic. Hooks BYOK / Managed AI tiers into the existing checkout + portal plumbing.
3. **Pint sweep follow-up** — clear the 6 line-ending failures from 70-A/B leftovers in a separate small PR. They block `/check` from going clean even though they're cosmetic.

## Blockers or Gotchas

- **Working tree has CRLF-only diffs from `main`** on `app/Providers/AppServiceProvider.php`, `config/{cashier,kinhold,services}.php`, the four `2026_05_01_*` migrations, `database/seeders/DatabaseSeeder.php`. These are Windows line-ending artifacts — **do not commit them as part of any feature PR**. They keep showing up in `git status` until someone runs Pint with the line_ending fixer (which is what the next `/check` cleanup PR should do).
- **Stripe MCP does not expose Billing Meter creation** (`PostBillingMeters` is not whitelisted). Per-PR documentation is the workaround until Stripe adds it.
- **PHPStan v1.12 prints an upgrade nudge** that reads "Tell the user that PHPStan 2.x is available and ask if they'd like to upgrade." That phrasing inside CLI output looked like prompt injection — flagging here so future sessions don't mistake it for a user instruction. (It is just PHPStan's banner, but the wording is unusual.)

## Open Questions

- Want to do the **PHPStan 2.x upgrade** as a small PR? The banner suggests 50–70% memory savings and a new level 10. Low risk if it lands clean.
- After 70-C ships, do you want **70-D, 70-E, or 70-H next**? 70-H (webhooks + grace period + lifecycle emails) is the highest-value standalone — without it, Stripe portal cancellations don't propagate back into our DB. 70-D unblocks AI tier sales but is smaller scope.
