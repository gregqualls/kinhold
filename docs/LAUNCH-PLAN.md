# Kinhold v1.0.0 Launch Plan — r/selfhosted Post Sunday April 12

> **Target:** r/selfhosted and open-source subreddits
> **Goal:** Try the demo at kinhold.app, then install it on your own server
> **NOT a consumer launch** — no revenue capture, no push to kinhold.app signups
> **Milestone:** [v1.0.0 Launch](https://github.com/gregqualls/kinhold/milestone/8)

---

## Issues in v1.0.0 Launch Milestone

| # | Title | Day | Priority | Status |
|---|-------|-----|----------|--------|
| [#139](https://github.com/gregqualls/kinhold/issues/139) | Self-hosted mode: skip landing page | Tue | P0 | TODO |
| [#140](https://github.com/gregqualls/kinhold/issues/140) | OG/Twitter meta tags for social sharing | Tue | P0 | TODO |
| [#141](https://github.com/gregqualls/kinhold/issues/141) | Quick fixes: license text, domain refs, 404 page | Tue | P0 | TODO |
| [#117](https://github.com/gregqualls/kinhold/issues/117) | Versioning, GitHub Releases, update notifications | Wed | P0 | TODO |
| [#142](https://github.com/gregqualls/kinhold/issues/142) | Docker polish: sessions, .dockerignore, env defaults | Wed | P1 | TODO |
| [#124](https://github.com/gregqualls/kinhold/issues/124) | Demo family outdated — refresh seed data | Thu | P1 | TODO |
| [#126](https://github.com/gregqualls/kinhold/issues/126) | Demo email verification bug | Thu | P1 | TODO |
| [#143](https://github.com/gregqualls/kinhold/issues/143) | Demo banner: "install on your server" CTA | Thu | P1 | TODO |

---

## Day-by-Day Schedule

### Day 1 — Tuesday: Social Card + Self-Hosted Mode (3-4 hrs)

**Goal:** When someone shares kinhold.app, it looks great. When someone installs it, they don't see marketing.

- [ ] **#139** — Add `self_hosted` flag to `/api/v1/config`. Router skips landing page when self-hosted.
- [ ] **#140** — Add OG/Twitter meta tags to `app.blade.php`. Create 1200×630 OG image.
- [ ] **#141** — Fix "MIT Licensed" → "Elastic License 2.0". Fix kinhold.com → kinhold.app. Add 404 route.

**Ship as PR, merge to main.**

### Day 2 — Wednesday: Versioning + Docker (4-5 hrs)

**Goal:** Self-hosters know what version they're running and can update. Docker experience is smooth.

- [ ] **#117** — Create `config/version.php` with v1.0.0. Expose in config API. Show in Settings. Optional: update check against GitHub Releases API.
- [ ] **#142** — Fix session persistence (database driver). Add `.dockerignore`. Consider production env defaults.
- [ ] Create GitHub Release v1.0.0 with release notes.

**Ship as PR, merge to main.**

### Day 3 — Thursday: Demo Experience (4-6 hrs)

**Goal:** Someone clicking "Try Demo" is immediately impressed and wants to install it.

- [ ] **#124** — Audit all 5 demo users end-to-end. Refresh seed data to be current and compelling.
- [ ] **#126** — Fix demo email verification. Demo users should never see verification banners.
- [ ] **#143** — Add demo banner: "You're exploring the demo — [Install Kinhold on Your Server →]"

**Ship as PR, merge to main.**

### Day 4 — Friday: Polish + Smoke Test (3-4 hrs)

**Goal:** Everything works end-to-end, both paths.

- [ ] Calendar empty state (quick fix — use existing EmptyState.vue component)
- [ ] OG card verification (test on Twitter Card Validator, Facebook Debugger, iMessage)
- [ ] **Full self-hosted smoke test:** Clone fresh → `./setup-simple.sh` → first-boot → register → onboarding → use app
- [ ] **Full demo smoke test:** kinhold.app → Try Demo → explore → see banner → click install link

### Day 5 — Saturday: Buffer + Launch Prep (2-3 hrs)

**Goal:** Everything is deployed, post is drafted, ready for Sunday.

- [ ] Final production deploy verification
- [ ] Draft Reddit post for r/selfhosted
- [ ] Fix any remaining issues found during smoke tests
- [ ] Tag v1.0.0 release on GitHub (if not done Wednesday)

---

## What's Explicitly Deferred (NOT this week)

| Item | Why |
|------|-----|
| GDPR account deletion (#96) | Self-hosters own their DB. Do before consumer launch. |
| AI chat rate limiting (#137) | Self-hosters use own API key. Not our cost. |
| Landing page separation (#134) | Self-hosted mode flag is sufficient for now. |
| Single-family enforcement (#138) | Can follow up post-launch. |
| Vault file uploads (#121) | Not visible in demo path. |
| Email template branding (#104) | Self-hosters configure own mail. |
| Stripe billing (#70) | No revenue capture this launch. |

---

## Session Workflow

Each day follows the standard pipeline:

```
/kickoff → code → /review → /check → /pr → /qa → /merge → /cleanup
```

Multiple issues can be bundled into a single PR per day if they're small and related (e.g., Day 1's three issues are all quick fixes).

---

## What "Success" Looks Like Sunday

1. Someone reads the Reddit post → clicks kinhold.app → sees a professional landing page with working OG preview
2. Clicks "Try Demo" → logs in as a parent → sees a populated dashboard with tasks, calendar, points, vault
3. Sees "Install on your server" banner → clicks → GitHub README with clear Docker setup
4. Runs `./setup-simple.sh` → app boots → first-boot redirects to register (no marketing page)
5. Creates family → onboarding wizard → functional app with all features
6. Checks Settings → sees v1.0.0 → knows they can check for updates
7. Comes back later → still logged in (sessions persist across restarts)
