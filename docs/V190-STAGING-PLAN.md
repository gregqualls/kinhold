# v1.9.0 Staging & Cutover Plan

> Reference doc for the v1.9.0 `BILLING_ENABLED=true` flip. Staging-first approach so we shake out billing in a real Upsun environment with sandbox Stripe before touching production.

**Date created:** 2026-05-04
**Owner:** Greg
**Related:** [#70](https://github.com/gregqualls/kinhold/issues/70) (closed), [#240](https://github.com/gregqualls/kinhold/issues/240) (promo codes follow-up)

---

## Why staging first

The local dev box has never run with `BILLING_ENABLED=true` against a real public URL Stripe can webhook into. Webhook signatures, OAuth redirect URLs, the Stripe Customer Portal return flow, and the paywall splash all interact with the deployed environment in ways `php artisan serve` can't simulate. A throwaway staging env on Upsun lets us prove the whole loop on sandbox Stripe before flipping production to live keys.

Production stays on `BILLING_ENABLED=false` throughout staging work. Zero risk to real users.

## Step 1 — Create staging environment on Upsun

```bash
# Branch staging from main (creates a new active environment)
upsun environment:branch staging main --project=2rozcvqjtjdta

# If the new env doesn't activate automatically:
upsun environment:activate staging --project=2rozcvqjtjdta

# Get the staging URL (will be something like staging-<hash>.<region>.upsunapp.com)
upsun environment:url --environment=staging --project=2rozcvqjtjdta
```

**Note the staging URL.** You'll need it for Stripe webhook config and the auth redirect allow-list.

## Step 2 — Set staging environment variables (Upsun Console, not CLI)

For secrets, prefer the Upsun web console (Project → staging environment → Variables) so they're never on disk locally. Set these as **environment-level variables, sensitive=true** so they don't inherit to feature branch envs:

| Variable | Value | Notes |
|---|---|---|
| `BILLING_ENABLED` | `true` | The flip. Staging only, not main. |
| `STRIPE_KEY` | `pk_test_...` | Sandbox publishable key |
| `STRIPE_SECRET` | `sk_test_...` | Sandbox secret key |
| `STRIPE_WEBHOOK_SECRET` | `whsec_...` | NEW webhook endpoint (created in step 3) |
| `STRIPE_PRICE_BASE_PLAN` | `price_test_...` | Sandbox base subscription price |
| `STRIPE_PRICE_AI_LITE` | `price_test_...` | Sandbox AI Lite tier |
| `STRIPE_PRICE_AI_STANDARD` | `price_test_...` | Sandbox AI Standard tier |
| `STRIPE_PRICE_AI_PRO` | `price_test_...` | Sandbox AI Pro tier |
| `APP_URL` | staging URL from step 1 | Critical for webhook + redirect URLs |

Production main keeps its existing values (no changes).

## Step 3 — Configure Stripe webhook for staging URL

Stripe Dashboard (still in test mode):

1. Developers → Webhooks → "Add endpoint"
2. Endpoint URL: `<staging-url>/stripe/webhook`
3. Events to send (the ones our `StripeWebhookController` handles):
   - `checkout.session.completed`
   - `customer.subscription.created`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`
   - `customer.subscription.trial_will_end`
   - `customer.updated`
   - `payment_method.attached`
   - `payment_method.detached`
4. Copy the signing secret (`whsec_...`) into the staging `STRIPE_WEBHOOK_SECRET` env var

Test mode and live mode have separate webhook endpoint lists in Stripe — staging uses test, production will eventually use live.

## Step 4 — Update Google OAuth allowed redirect URIs (if testing OAuth)

If you want to test the Google Calendar / Login flows on staging:

- Google Cloud Console → APIs & Services → Credentials → OAuth 2.0 Client
- Add the staging URL to "Authorized redirect URIs"
- Don't remove the production URL

## Step 5 — Seed the staging database

After Upsun finishes the first deploy, the staging DB will be empty:

```bash
upsun ssh --environment=staging --project=2rozcvqjtjdta
php artisan migrate --force
php artisan app:refresh-demo
```

Now you have the demo family on staging.

## Step 6 — Smoke test (the actual point)

Sign up a fresh test family on staging (NOT the demo family — demo billing is now blocked by [#239](https://github.com/gregqualls/kinhold/pull/239)):

1. **Onboarding plan picker** (70-G): pick a plan, get pushed to Stripe Checkout, complete with `4242 4242 4242 4242`, return to app, confirm subscription is `active` (or `trialing` if trial config is on)
2. **BillingPanel in Settings**: card on file shows correctly, lifecycle dates render
3. **Customer Portal**: click "Manage payment", land on Stripe-hosted portal, change card, return cleanly
4. **AI tier picker**: switch from Lite → Standard mid-trial, verify the trial-end logic in #230 fires (Stripe-side proration kicks in)
5. **Cancel → resume**: cancel from portal, see the cancelled state in our DB, resume before `ends_at`, watch it flip back to active
6. **Past-due simulation**: in Stripe Dashboard → Subscriptions, manually set the test subscription's status to `past_due`, reload the SPA, confirm the paywall splash from #237 renders with "Update payment method" CTA
7. **Trial expired simulation**: in Stripe, end the trial without a payment method, watch the paywall flip to "Welcome back" with "Reactivate" CTA
8. **Demo family billing lockdown** (#239): log in as `parent@demo.local` on staging, confirm Settings has no Billing section, confirm `POST /api/v1/billing/checkout-session` returns 403
9. **Webhook reliability**: in Stripe → Webhooks, watch the events page for staging — confirm 200 responses on every event we care about

If any step fails, fix in code, push to `main` (which also redeploys staging since it tracks main + the staging env override), repeat.

## Step 7 — Production cutover (next session, after staging passes)

When staging is solid:

1. **Flip Stripe to live mode** in the Stripe Dashboard
2. **Recreate Products + Prices in live mode** (sandbox IDs do NOT carry over to live)
3. **Create live webhook endpoint** pointing at `https://kinhold.app/stripe/webhook`, capture the live `whsec_...`
4. **Update production env vars** in Upsun Console (main environment):
   - `STRIPE_KEY` → `pk_live_...`
   - `STRIPE_SECRET` → `sk_live_...`
   - `STRIPE_WEBHOOK_SECRET` → live webhook secret from step 3
   - `STRIPE_PRICE_*` → live price IDs from step 2
   - `BILLING_ENABLED=true` (the actual flip)
5. **Smoke production with a real card** (your own, refund yourself after)
6. **Tag `v1.9.0` GitHub Release** with full changelog (already in `CHANGELOG.md`)
7. **Tear down staging** to stop the meter:
   ```bash
   upsun environment:delete staging --project=2rozcvqjtjdta
   ```

## Step 8 — Watch the first 24 hours

- Stripe Dashboard → first real customers should appear
- Upsun logs → no 500s on `/stripe/webhook`
- Sentry / error tracking → any auth or paywall regressions
- If something breaks, you can flip `BILLING_ENABLED=false` instantly without a deploy (it's an env var, takes ~30s to propagate)

## Rollback plan

If production cutover goes sideways:

1. Set `BILLING_ENABLED=false` in Upsun (instant)
2. SPA falls back to free-for-everyone behavior, paywall never mounts, BillingPanel hides
3. Existing Stripe subscriptions keep ticking on Stripe's side but our app ignores them
4. Triage in staging, reflip when ready

The only irreversible part is real customer charges. Refund through Stripe Dashboard if anyone got billed during a problem window.

---

## Quick reference

- **Project ID:** `2rozcvqjtjdta`
- **Production URL:** https://kinhold.app
- **Staging URL:** TBD after step 1
- **Stripe Dashboard:** dashboard.stripe.com (test mode toggle in upper-right)
- **Upsun CLI auth:** `upsun auth:browser-login` if commands fail
