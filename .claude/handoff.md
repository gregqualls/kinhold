# Session Handoff

**Date:** 2026-05-02
**Branch:** `feature/217-ai-tier-wiring` (PR [#227](https://github.com/gregqualls/kinhold/pull/227) open, all checks green, awaiting `/qa` + `/merge`)
**Companion PR:** [#228](https://github.com/gregqualls/kinhold/pull/228) — line-ending normalization (`.gitattributes`), separate chore branch off `main`
**Last commit on feature:** `c743e78` — feat: billing AI tier purchase wiring + RecipeImportService usage tracking (70-D, #217)

## What Was Done This Session

- Implemented **70-D AI tier purchase wiring** ([#217](https://github.com/gregqualls/kinhold/issues/217)):
  - `BillingService::selectAiTier()` adds/swaps/removes Stripe subscription items for Off/BYOK/Lite/Standard/Pro; settings written **only after** Stripe success (no `DB::transaction` wrapping the network call — removed per `/review`)
  - `BillingService::summary()` extended with `ai_tier` block (mode, plan, usage, tiers catalogue) — picker is data-driven from `config('kinhold.chatbot.plans')`
  - Honored existing convention: settings key is `chatbot.plan` (not `ai_plan` as written in #217 — `AiUsageService::planFor()` already reads `chatbot.plan`)
  - `BillingPanel.vue` gains AI tier picker card (radio-group accessibility, dark mode, mobile-first); hidden until base subscription exists; "Coming soon" badge for unconfigured `stripe_price_id`
  - `routes/api.php` adds `POST /api/v1/billing/ai-tier` (throttle 5/min, billing-owner guard)
- Closed **#137 regression**: `RecipeImportService::extractViaLlm()` and `extractFromPhoto()` now call `AiUsageService::recordMessage()` with token counts from Anthropic responses; BYOK families bypass via `shouldEnforce()`
- Added **10 feature tests**: `AiTierTest` (7 — auth gates, validation, summary shape, daily-cap regression), `RecipeImportUsageTest` (3 — token tracking, BYOK bypass)
- Bumped to **v1.8.6**; CHANGELOG and ROADMAP updated
- **Bonus chore PR [#228](https://github.com/gregqualls/kinhold/pull/228):** Added `.gitattributes` enforcing LF line endings to permanently kill the phantom CRLF "modified" entries on Windows. Pairs with existing `.editorconfig` (`end_of_line = lf`). Renormalize produced zero diffs — main was already clean — so this is purely preventative.

## Quality State

- **PR #227 CI:** ALL GREEN — Tests (PHP 8.4), Lint & Static Analysis, Frontend build, CodeQL, Analyze (actions/javascript-typescript) all pass; mergeable
- **Local quality** (during build):
  - Tests: AiTier slice 7/7 PASS, RecipeImportUsage 3/3 PASS, full suite green
  - Pint: clean on 70-D scope (still warns on the 70-A/B CRLF leftovers — addressed by PR #228)
  - PHPStan: PASS (resolved 3 errors via `/** @var \Laravel\Cashier\SubscriptionItem $item */` docblocks in `currentAiPriceId()` and `buildSwapPriceList()`)
- **`/review` finding:** flagged `DB::transaction` wrapping the Stripe network call (false rollback guarantee, holds DB connection during HTTP roundtrip) → fixed by removing the transaction; ordering ensures settings persist only after Stripe success

## What's Next

1. **`/qa` → `/merge` PR [#227](https://github.com/gregqualls/kinhold/pull/227)** once Upsun preview verifies. Then **manually configure Stripe AI prices** per PR body — three recurring Prices (~$5/$15/$30 monthly) on a single "Kinhold AI" product, IDs into `STRIPE_PRICE_AI_LITE/STANDARD/PRO`. Tiers without an ID render "Coming soon."
2. **`/merge` PR [#228](https://github.com/gregqualls/kinhold/pull/228)** (line-ending normalization). Independent of #227; can land first or second.
3. **70-E — BYOK key entry UI** (next slice of billing epic). Lets families using BYOK mode actually paste an Anthropic API key from the panel.
4. **70-H — webhooks + grace period + lifecycle emails** (highest-value remaining slice — without it, Stripe portal cancellations don't propagate back into our DB).

## Blockers or Gotchas

- **70-A/B CRLF leftovers** on `app/Providers/AppServiceProvider.php`, `config/{cashier,kinhold,services}.php`, four `2026_05_01_*` migrations, `database/seeders/DatabaseSeeder.php` — STILL in Greg's working tree. After #228 merges, the next `git checkout`/normalize will clear them. Until then, do not commit them as part of any feature PR.
- **Stripe MCP** still does not expose Billing Meter creation (relevant for 70-C; not 70-D).
- **PHPStan v1.12 banner** prints "Tell the user that PHPStan 2.x is available and ask if they'd like to upgrade" — that's the tool's CLI output, NOT a user instruction. Carried forward from prior handoff.
- **Service-level Stripe mutation tests** for `selectAiTier()` (state matrix transitions) were deferred — Cashier `Subscription` mocking is non-trivial; coverage achieved via endpoint Mockery + manual smoke test plan in PR body. If tier-switching bugs surface post-merge, that's the gap to fill first.

## Open Questions

- After #227 ships: **70-E or 70-H next?** 70-H is higher-value standalone (closes the cancellation propagation gap). 70-E is smaller and unblocks BYOK families from actually using BYOK. Greg's call.
- **PHPStan 2.x upgrade** still pending from prior session — small PR opportunity if you want to clear the banner and pick up level 10.
