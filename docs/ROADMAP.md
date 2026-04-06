# Kinhold — Product Roadmap

> **Canonical tracking:** [GitHub Milestones](https://github.com/gregqualls/kinhold/milestones)
> This file provides an overview. Individual issues on GitHub have full specs and discussion.
>
> Last updated: 2026-04-06

---

## What's Already Built (MVP Complete)

These features are live and working:

| Feature | Status |
|---------|--------|
| Email/password auth + Google OAuth (Sanctum + Socialite) | COMPLETE |
| Family creation, invite codes, parent/child roles | COMPLETE |
| Managed child accounts (no email required) | COMPLETE |
| Task management (CRUD, tags, priority, recurring via RRULE) | COMPLETE |
| Family calendar (read-only Google Calendar + ICS, manual events, recurrence, visibility modes) | COMPLETE |
| Encrypted family vault (WYSIWYG markdown, per-item permissions, document uploads, playbooks) | COMPLETE |
| AI chatbot (Claude-powered, queries calendar/tasks/vault) | COMPLETE |
| MCP server (20 Laravel-native tools, full content coverage via Claude Desktop/Code) | COMPLETE |
| Gamification (points ledger, kudos, leaderboard, rewards store with auctions, badges) | COMPLETE |
| Email notifications (weekly digest, task assigned/completed, invites) | COMPLETE |
| Dark mode (full support across all views) | COMPLETE |
| Mobile-first responsive UI | COMPLETE |
| Profile pictures (upload, 26 presets, 12 color picker, Google photo) | COMPLETE |
| Onboarding wizard (5-step parent setup, simplified join flow) | COMPLETE |
| Google account linking + email verification | COMPLETE |
| Production deploy on Upsun (auto-deploy from GitHub) | COMPLETE |
| Open source on GitHub (Elastic License 2.0) | COMPLETE |
| Self-hosting infrastructure (SQLite, Docker, graceful degradation) | COMPLETE |
| GitHub Actions CI + SDLC pipeline (10 slash commands) | COMPLETE |
| Community docs (CODE_OF_CONDUCT, SECURITY, PR template) | COMPLETE |
| Security audit (cross-family isolation, OAuth hardening, rate limiting) | COMPLETE |
| Unified policy-based auth (8 Laravel Policies for API + MCP) | COMPLETE |
| Calendar event visibility (visible/busy/private modes) | COMPLETE |

---

## Phase A: Make It Solid

**Goal:** Foundational work — GDPR compliance, landing page separation, bug fixes, and quality-of-life improvements before the next feature push.

| Issue | Title | Priority |
|-------|-------|----------|
| [#96](https://github.com/gregqualls/kinhold/issues/96) | GDPR compliance: account deletion and data export | Critical |
| [#134](https://github.com/gregqualls/kinhold/issues/134) | Separate landing page from SPA | High |
| [#135](https://github.com/gregqualls/kinhold/issues/135) | Refactor /check command: shell script + haiku reporter | Medium |
| [#117](https://github.com/gregqualls/kinhold/issues/117) | Versioning, GitHub Releases, update notifications | High |
| [#121](https://github.com/gregqualls/kinhold/issues/121) | Bug: file uploads to the vault | High |
| [#124](https://github.com/gregqualls/kinhold/issues/124) | Bug: demo family outdated | Medium |
| [#126](https://github.com/gregqualls/kinhold/issues/126) | Bug: demo email verification | Medium |
| [#104](https://github.com/gregqualls/kinhold/issues/104) | Brand colors in transactional email templates | Low |
| [#99](https://github.com/gregqualls/kinhold/issues/99) | Show Kinhold icon in Claude Desktop MCP connector | Low |

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
| [#122](https://github.com/gregqualls/kinhold/issues/122) | Calendar features (additional calendar capabilities) | Medium |
| [#125](https://github.com/gregqualls/kinhold/issues/125) | Add to a kudo (enhance kudos system) | Medium |
| [#72](https://github.com/gregqualls/kinhold/issues/72) | Family announcements / bulletin board | Medium |
| [#26](https://github.com/gregqualls/kinhold/issues/26) | Rearrange the dashboard | Medium |

---

## Phase D: Make It Grow

**Goal:** Differentiating features that attract new users and generate press/word-of-mouth.

| Issue | Title | Priority |
|-------|-------|----------|
| [#73](https://github.com/gregqualls/kinhold/issues/73) | Allowance & money tracking for kids | Medium |
| [#74](https://github.com/gregqualls/kinhold/issues/74) | AI photo-to-calendar: snap a flyer, extract events | High |
| [#25](https://github.com/gregqualls/kinhold/issues/25) | Voting on stuff | Low |

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

## Phase F: Food & Meal Planning

**Goal:** Shopping lists, meal planning, and grocery AI integrations. Multi-week effort — not blocking MVP or foundational work.

| Issue | Title | Priority |
|-------|-------|----------|
| [#65](https://github.com/gregqualls/kinhold/issues/65) | Shopping & grocery lists | Very High |
| [#66](https://github.com/gregqualls/kinhold/issues/66) | Meal planning | High |
| [#67](https://github.com/gregqualls/kinhold/issues/67) | AI chatbot: meal and grocery integrations | Medium |

---

## Future Ideas (Parking Lot)

> Ideas captured from competitive analysis and brainstorming. Not planned yet.

- Plugin/module system for community extensions (under investigation)
- Staging environment strategy (discussion pending)
- Vault overhaul (explore Obsidian-like or Standard Notes integration)
- Vault audit log and version history
- More calendar providers (Outlook, iCloud, CalDAV)
- Family chat/messaging (beyond announcements)
- Location sharing
- Chore rotation system
- Budget & expense tracking
- School schedule integration
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
