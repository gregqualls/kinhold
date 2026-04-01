# Kinhold — Product Roadmap

> **Canonical tracking:** [GitHub Milestones](https://github.com/gregqualls/kinhold/milestones)
> This file provides an overview. Individual issues on GitHub have full specs and discussion.
>
> Last updated: 2026-04-01

---

## What's Already Built (MVP Complete)

These features are live and working:

| Feature | Status |
|---------|--------|
| Email/password auth + Google OAuth (Sanctum + Socialite) | COMPLETE |
| Family creation, invite codes, parent/child roles | COMPLETE |
| Managed child accounts (no email required) | COMPLETE |
| Task management (CRUD, tags, priority, recurring via RRULE) | COMPLETE |
| Family calendar (read-only Google Calendar + ICS aggregation) | COMPLETE |
| Encrypted family vault (categories, per-item permissions, document uploads) | COMPLETE |
| AI chatbot (Claude-powered, queries calendar/tasks/vault) | COMPLETE |
| MCP server (18 Laravel-native tools, full content coverage via Claude Desktop/Code) | COMPLETE |
| Gamification (points ledger, kudos, leaderboard, rewards store) | COMPLETE |
| Badges (auto-triggered + custom, hidden badges) | COMPLETE |
| Email notifications (weekly digest, task assigned/completed, invites) | COMPLETE |
| Dark mode (full support across all views) | COMPLETE |
| Mobile-first responsive UI | COMPLETE |
| Production deploy on Upsun (auto-deploy from GitHub) | COMPLETE |
| Open source on GitHub (Elastic License 2.0) | COMPLETE |
| Self-hosting infrastructure (SQLite, simple Docker, graceful degradation) | COMPLETE |
| GitHub Actions CI (PHPUnit + Vite build + lint on PR/push) | COMPLETE |
| SDLC pipeline (10 slash commands, 10 quality gates) | COMPLETE |
| Community docs (CODE_OF_CONDUCT, SECURITY, PR template) | COMPLETE |

---

## Phase 0: Foundations

**Goal:** Establish core principles, fix usability, and prepare the product identity before adding more features.

> *"We have feature bloat risk. Make what we have easy to use before building more."*

| Issue | Title | Priority |
|-------|-------|----------|
| [#61](https://github.com/gregqualls/kinhold/issues/61) | Define core product principles | High |
| [#62](https://github.com/gregqualls/kinhold/issues/62) | Branding: evaluate project name and identity | High |
| [#63](https://github.com/gregqualls/kinhold/issues/63) | Onboarding wizard for new families | High |
| [#64](https://github.com/gregqualls/kinhold/issues/64) | Chat: show setup prompt when no API key configured | Medium |
| [#75](https://github.com/gregqualls/kinhold/issues/75) | MCP server: full API parity (points, rewards, badges, settings) — **18 tools built, pending TS cleanup** | Critical |
| [#76](https://github.com/gregqualls/kinhold/issues/76) | Claude connector: manage Kinhold from Claude.ai chat | High |
| [#77](https://github.com/gregqualls/kinhold/issues/77) | New feature checklist: enforce MCP, docs, landing page updates | High |
| ~~#78~~ | ~~Database seeder~~ — merged into #77 | — |

---

## Chat Improvements Backlog (newly created issues)

| Issue | Title | Priority |
|-------|-------|----------|
| [#106](https://github.com/gregqualls/kinhold/issues/106) | Chat: expand context (calendar events, badges, points) | High |
| [#107](https://github.com/gregqualls/kinhold/issues/107) | Chat: child safety — permissions, content filtering, moderation | **Critical (pre-Corey)** |
| [#108](https://github.com/gregqualls/kinhold/issues/108) | Chat: hidden badges spoiled — don't reveal unearned badge details | Medium |
| [#109](https://github.com/gregqualls/kinhold/issues/109) | Chat: stateless messages — pass conversation history to AI | High |

---

## Phase A: Make It Sticky

**Goal:** Add the daily-driver features that make families open the app every day.

> Shopping lists are the #1 most-used feature across every competitor. Meal planning drives grocery lists, which drive daily opens. These two features go hand-in-hand.

| Issue | Title | Priority |
|-------|-------|----------|
| [#65](https://github.com/gregqualls/kinhold/issues/65) | Shopping & grocery lists | Very High |
| [#66](https://github.com/gregqualls/kinhold/issues/66) | Meal planning | High |
| [#67](https://github.com/gregqualls/kinhold/issues/67) | AI chatbot: meal and grocery integrations | Medium |

---

## Phase B: Make It Reachable

**Goal:** Get the app onto phones, enable notifications, and start accepting payments.

| Issue | Title | Priority |
|-------|-------|----------|
| [#68](https://github.com/gregqualls/kinhold/issues/68) | PWA support (service worker, manifest, installable) | Very High |
| [#69](https://github.com/gregqualls/kinhold/issues/69) | Web push notifications | Very High |
| [#70](https://github.com/gregqualls/kinhold/issues/70) | Stripe billing & subscription system | Critical |

**Pricing model:**
- Self-hosted: Free forever (Elastic License 2.0)
- Hosted (BYOK): $4.99/mo or $49.99/yr — bring your own Anthropic API key
- Hosted (Managed AI): $9.99/mo — we provide the Claude API access
- The only upsell is the API key. No feature gating.

---

## Phase C: Make It Complete

**Goal:** Close the biggest functional gaps that prevent Kinhold from being a true family command center.

| Issue | Title | Priority |
|-------|-------|----------|
| [#71](https://github.com/gregqualls/kinhold/issues/71) | Two-way Google Calendar sync (create/edit events) | High |
| [#93](https://github.com/gregqualls/kinhold/issues/93) | Show tasks with due dates on the calendar view | Medium |
| [#72](https://github.com/gregqualls/kinhold/issues/72) | Family announcements / bulletin board | Medium |

---

## Phase D: Make It Grow

**Goal:** Differentiating features that attract new users and generate press/word-of-mouth.

| Issue | Title | Priority |
|-------|-------|----------|
| [#73](https://github.com/gregqualls/kinhold/issues/73) | Allowance & money tracking for kids | Medium |
| [#74](https://github.com/gregqualls/kinhold/issues/74) | AI photo-to-calendar: snap a flyer, extract events | High |

---

## Phase E: Make It Visible

**Goal:** Public launch and community building.

- Launch on r/selfhosted, Hacker News, Product Hunt
- README polish with screenshots and demo video
- One-click deploy options (Docker, Upsun, Railway)
- Blog content: "Why we built an open-source family hub"
- Family dashboard mode (optimized for a cheap tablet on the wall)
- Branding finalized and applied everywhere

---

## Future Ideas (Parking Lot)

> Ideas captured from competitive analysis and brainstorming. Not planned yet.

- Vault overhaul (explore Obsidian-like or Standard Notes integration)
- Vault audit log and version history
- More calendar providers (Outlook, iCloud, CalDAV)
- Family chat/messaging (beyond announcements)
- Location sharing
- Chore rotation system
- Budget & expense tracking
- School schedule integration
- Plugin/module system for community extensions
- Mobile app (React Native or Capacitor wrapper)
- Internationalization (i18n)
- Accessibility audit (WCAG 2.1 AA)
- Two-factor authentication / passkeys
- Family photo gallery
- Emergency info page
- Shared-with-external vault (share with doctor, lawyer, school)
- Proactive AI reminders
- Webhook integrations (IFTTT, Zapier, Home Assistant)

---

## Strategic Positioning

> **"The open-source family hub that respects your privacy and actually gets smarter over time."**

Three pillars no competitor combines:
1. **Privacy-first** — self-hosted, encrypted vault, no ads, open source
2. **AI-powered** — Claude chatbot queries your family data, MCP server for automation
3. **Gamification that works** — points, badges, rewards that motivate the whole family

Target audience (in go-to-market order):
1. Self-hosters and privacy-conscious tech parents (r/selfhosted, HN, GitHub)
2. AI-forward families who already use Claude/ChatGPT
3. Parents frustrated with Cozi's decline looking for a modern alternative
