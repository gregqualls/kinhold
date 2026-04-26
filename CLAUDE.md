# Kinhold — Project Briefing

> Auto-loaded at session start. Keep this lean. Detail lives in `docs/`.

## What is Kinhold?

Open-source family hub at **kinhold.app** — calendar, tasks, vault, AI assistant, gamification for the Qualls family (and other families who self-host).

## Owner

Greg Qualls. ADHD — capture stray ideas in `docs/ROADMAP.md`, stay on the agreed session goal. Strong Laravel; frame Vue/Tailwind in Laravel/PHP analogues. Personal context in `CLAUDE.local.md`.

## Tech Stack

| Layer | Tech |
|---|---|
| Backend | Laravel 11 (PHP 8.2+), REST API only |
| Frontend | Vue 3 SPA (Composition API, `<script setup>`), Pinia, Tailwind |
| DB / Cache | PostgreSQL 16 (UUIDs), Redis 7 |
| Auth | Sanctum + Socialite (Google OAuth) |
| MCP | Laravel-native `laravel/mcp` at `/mcp`, 20 tools |
| Hosting | Upsun (auto-deploy from `main`), Cloudflare DNS |

## Architecture Principles

1. **Modular** — features self-contained (controller, model, service, store, views).
2. **API-first** — SPA and MCP server are equal consumers. Never bypass the API from the frontend.
3. **Mobile-first** — design for 375px first.
4. **Self-hostable by default** — gate operational complexity, not features. Self-hosted gets 100% of core.
5. **Security-conscious** — vault encrypted at rest, role + per-item permissions, no secrets in code.
6. **Open source** — Elastic License 2.0.

## Where to find things

- **Module deep-dives, DB schema, API routes, file layout, dev setup** → `docs/REFERENCE.md`
- **Architectural decisions (with reasoning)** → `docs/ARCHITECTURE.md`
- **Roadmap, phases, aspirational features** → `docs/ROADMAP.md`
- **Conventions** → `docs/CONVENTIONS.md`
- **Recent session work** → `CHANGELOG.md`
- **Branding** → `docs/BRAND_GUIDE.md`
- **Food feature plan** → `docs/FOOD-FEATURES-SPEC.md`, `docs/FOOD-IMPLEMENTATION-PLAN.md`
- **Self-hosting** → `SELF-HOSTING.md`

## Current Phase

MVP shipped to production. Currently in **Phase F: Food & Meal Planning** — Steps 1–7 done, Step 8 (MCP tools for food/meals) remaining. Other open Phase A items: landing page split (#134), AI usage limits (#137), self-host single-family enforcement (#138). See `docs/ROADMAP.md`.

## Session Rules

1. **Read this file, then `docs/ROADMAP.md` and `CHANGELOG.md`.** Ask Greg what to focus on before starting.
2. **Don't scope-creep.** Capture new ideas in ROADMAP, stay on the agreed goal.
3. **API-first, mobile-first, security-aware.** This app stores SSNs and medical info — treat every feature accordingly.
4. **After completing work, update** `CHANGELOG.md`. Update `docs/ARCHITECTURE.md` if a decision changed; update `docs/REFERENCE.md` if a module/schema/route changed; update `docs/ROADMAP.md` if phases shifted.
5. **Pipeline:** `/kickoff` → code → `/review` → `/check` → `/pr` → `/qa` → `/handoff` → `/merge` → `/cleanup`. `/check` must pass before `/pr`.
6. **Upsun CLI auth expires** — if `upsun` commands fail, ask Greg to run `upsun auth:browser-login`.
7. **Never `upsun push`** — push to GitHub; Upsun auto-deploys.
