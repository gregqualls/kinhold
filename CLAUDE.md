# Kinhold — Project Context for Claude

> This file is automatically read at the start of every Claude session.
> Keep it updated after every working session. It is the single source of truth.

## Project Owner

Greg Qualls (glqualls@gmail.com)
- Has ADHD — will have many ideas, needs help staying focused on priorities
- Familiar with Laravel, prefers clear explanations for frontend/Vue concepts
- Family: wife + 3 kids (ages 12, 14, 17 as of March 2026)

## What Is Kinhold?

An open-source family hub web application at **kinhold.app**. It's a central place for the Qualls family (and eventually other families) to manage their shared life — calendar, tasks, important documents, and quick AI-powered answers about family data.

## Tech Stack

| Layer | Technology | Notes |
|-------|-----------|-------|
| Backend | Laravel 11 (PHP 8.2+) | REST API only, no Blade rendering except SPA shell |
| Frontend | Vue 3 (Composition API, `<script setup>`) | Full SPA, communicates via `/api/v1/` |
| State | Pinia | One store per module |
| Styling | Tailwind CSS | Mobile-first, card-based UI |
| Database | PostgreSQL 16 | UUIDs for primary keys |
| Cache/Queue | Redis 7 | Sessions, cache, queue driver |
| Auth | Laravel Sanctum + Socialite | Cookie SPA, token MCP, Google OAuth via Socialite |
| Encryption | Laravel app-level | Vault sensitive fields encrypted at rest |
| MCP Server | Laravel-native (`laravel/mcp`) | 18 tools at `/mcp`, Sanctum bearer token auth |
| Local Dev | Homebrew (preferred) or Docker | `brew install php composer postgresql redis` then `php artisan serve` |
| Production | Upsun.com | Config in `.upsun/config.yaml` |
| Domain | kinhold.app | Configured via Cloudflare |

## Architecture Principles

1. **Modular** — Each feature is self-contained (controller, model, service, store, views). New features plug in without touching existing code.
2. **API-first** — Everything goes through the REST API. The SPA and MCP server are equal consumers.
3. **Mobile-first** — Design for 375px phone screens first, scale up to desktop.
4. **Open source friendly** — Elastic License 2.0, Docker one-click setup, `.env` for all config.
5. **Self-hostable by default** — We don't gate features — we gate operational complexity. The self-hosted version gets 100% of core features. Cloud (kinhold.app) adds managed services (OAuth, email, backups), not exclusive functionality.
6. **Security-conscious** — Sensitive vault data encrypted at rest, role-based + per-item permissions, no secrets in code.
7. **Future-proof** — Data models support future features (recurring tasks, kanban boards, budgets, etc.) even if the UI doesn't expose them yet.

## Modules (Current State)

### 1. Authentication (MVP — SCAFFOLDED)
- Email/password with Sanctum
- Family creation on registration OR join via invite code
- Roles: `parent` (full access) and `child` (restricted)
- Google OAuth login via Laravel Socialite (redirect → callback → Sanctum token → SPA pickup)
- **Future:** passkeys, 2FA

### 2. Family Calendar (MVP — SCAFFOLDED)
- **Current:** Read-only aggregation of Google Calendars for all 5 family members
- Each member connects their own Google Calendar in Settings
- Color-coded events per family member
- Month/week/day views, mobile-optimized day list
- **Future:** Two-way sync (create/edit events from hub), more providers (Outlook, iCloud)

### 3. Tasks & To-Dos (MVP — SCAFFOLDED + GAMIFICATION)
- Task lists (e.g., "Grocery", "House Projects", "School")
- Tasks with: title, description, assignee, due date, priority (low/medium/high), points
- Family tasks (anyone can complete) vs personal tasks
- Recurring tasks via RRULE (daily, weekly+day, monthly+date) — artisan command generates instances 7 days ahead
- Points awarded on completion, reversed on uncomplete
- **Future:** Categories, kanban boards, subtasks, dependencies

### 4. Family Vault (MVP — SCAFFOLDED)
- Categories: Medical, Financial, Insurance, Legal, Education, Personal
- Structured data entries (key-value pairs, encrypted at rest)
- Document uploads (PDFs, images)
- **Permissions model:** Parent/child base roles + per-item overrides. Parents see everything. Children see only entries explicitly shared with them.
- Sensitive fields masked by default, tap to reveal, copy with auto-clear
- **Future:** Version history, audit log, shared-with-external (doctor, lawyer)

### 5. AI Chatbot (MVP — SCAFFOLDED)
- Drop in an Anthropic API key in Settings to enable
- Natural language queries against family data: "What's for dinner Tuesday?", "What's my SSN?", "What tasks are due this week?"
- Claude API on the backend queries calendar, tasks, and vault
- Suggested quick questions, chat history
- **Future:** Proactive reminders, family digest emails

### 6. MCP Server (IMPLEMENTED — Laravel-Native)
- Laravel-native via `laravel/mcp` package — runs at `/mcp` endpoint, no separate process
- 18 consolidated tools (action-based) covering all modules: tasks, points, rewards, badges, calendar, vault, featured events, family, settings, search
- Authenticates with Sanctum bearer token
- Direct model/service access (no HTTP round-trips)
- `ScopesToFamily` trait scopes all queries to authenticated user's family
- Parent-only write actions enforced at tool level
- Greg manages Kinhold entirely through Claude Desktop or Claude Code
- **Legacy:** `mcp-server/` TypeScript directory still exists but is superseded
- **Future:** Webhooks, real-time sync

### 7. Points & Gamification (IMPLEMENTED)
- **Ledger-based points system:** Point Bank = SUM(all transactions). Never mutate a balance — append transactions.
- Transaction types: task_completion, task_reversal, kudos, deduction, redemption, adjustment
- Kudos: any family member can give +1 pt kudos with a reason
- Deductions: parents only, negative points with reason
- **Leaderboard:** Ranked by positive points within configurable period (daily/weekly/monthly). Resets each period. Does NOT affect the bank.
- **Rewards store:** Parent-created prizes (Sweets=10pts, TV Time=20pts, etc.). Instant purchase — no approval flow.
- **Activity feed:** Family-wide scrollable feed showing all point events
- **Feature toggles:** Parents can enable/disable points and badges modules in Settings

### 8. Badges (IMPLEMENTED)
- Steam-style achievements — purely for fun/bragging rights
- Auto-triggered by thresholds: points_earned, tasks_completed, task_streak, kudos_received/given, rewards_purchased, login_streak
- Custom badges: manually awarded by parents
- Hidden badges: show as "???" until earned (surprise mechanic)
- Hexagonal badge icons with accent color glow, 20 preset SVG icons
- 10 preset badges seeded per family + parents can create more
- BadgeService checks thresholds after every point/task event

## File Structure (Key Directories)

```
kinhold/
├── app/
│   ├── Http/Controllers/Api/V1/   # 12 controllers (Auth, Task, TaskList, Vault, Calendar, Chat, Family, Settings, Points, Rewards, Badges, base)
│   ├── Http/Requests/             # Form request validation (Auth/, Task/, Vault/)
│   ├── Http/Resources/            # API response transformers
│   ├── Models/                    # 14 Eloquent models (incl. PointTransaction, Reward, RewardPurchase, Badge)
│   ├── Services/                  # Business logic (GoogleCalendar, VaultEncryption, Chatbot, Points, Badge)
│   ├── Policies/                  # Authorization (Task, TaskList, VaultEntry, Family, Badge, Tag, Reward, FeaturedEvent)
│   ├── Console/Commands/          # Artisan commands (GenerateRecurringTasks)
│   ├── Enums/                     # FamilyRole, TaskPriority, PermissionLevel, PointTransactionType, BadgeTriggerType
│   └── Mcp/                      # Laravel-native MCP server (Servers/, Tools/, Tools/Concerns/)
├── database/migrations/           # 17 migrations (timestamped)
├── resources/js/
│   ├── components/                # Reusable Vue components (layout/, common/, calendar/, tasks/, vault/, chat/, points/, badges/)
│   ├── views/                     # Page-level Vue components (auth/, dashboard/, calendar/, tasks/, vault/, chat/, settings/, points/, badges/)
│   ├── stores/                    # Pinia stores (auth, tasks, vault, calendar, chat, points, badges)
│   ├── services/                  # API client (axios)
│   ├── composables/               # Vue composables (useNotification, useFamilyColors)
│   └── router/                    # Vue Router config
├── routes/ai.php                  # MCP route registration (/mcp endpoint)
├── docker/                        # Nginx, PHP config, entrypoint
├── docs/                          # Architecture, roadmap, conventions
└── .upsun/                        # Production deployment config
```

## API Route Map

All routes are prefixed with `/api/v1/`. Auth routes are public, everything else requires Sanctum auth.

**Auth:** `POST /register`, `POST /login`, `POST /logout`, `GET /user`
**Tasks:** CRUD on `/tasks` and `/task-lists`, plus `PATCH /tasks/{id}/toggle`
**Vault:** CRUD on `/vault/entries` and `/vault/categories`, permissions at `/vault/entries/{id}/permissions`, documents at `/vault/entries/{id}/documents`
**Calendar:** `GET /calendar/events`, `GET /calendar/connections`, `POST /calendar/connect`, `POST /calendar/sync`
**Family:** `GET /family`, `GET /family/members`, `POST /family/invite`, `PUT /family/settings`
**Chat:** `POST /chat`, `GET /chat/history`
**Settings:** `GET /settings`, `PUT /settings`
**Points:** `GET /points/bank`, `GET /points/leaderboard`, `GET /points/feed`, `POST /points/kudos`, `POST /points/deduct`
**Rewards:** CRUD on `/rewards`, `POST /rewards/{id}/purchase`, `GET /rewards/purchases`
**Badges:** CRUD on `/badges`, `POST /badges/{id}/award`, `DELETE /badges/{id}/revoke/{user}`, `GET /badges/earned`

## Database Schema (Key Tables)

- `families` — id, name, slug, invite_code, settings (JSON)
- `users` — id, family_id, name, email, password, family_role (enum), avatar, avatar_color, google_avatar, date_of_birth, timezone
- `tasks` — id, family_id, created_by, assigned_to, title, description, due_date, completed_at, priority (enum), is_family_task, points, recurrence_rule, recurrence_end, parent_task_id
- `vault_categories` — id, family_id, name, slug, icon, description
- `vault_entries` — id, family_id, vault_category_id, created_by, title, encrypted_data (encrypted JSON), notes, metadata (JSON)
- `vault_permissions` — id, vault_entry_id, user_id, permission_level (enum: view/edit)
- `documents` — id, documentable_type/id (polymorphic), uploaded_by, original_filename, stored_filename, mime_type, size, disk, path, encrypted
- `calendar_connections` — id, user_id, provider, access_token (encrypted), refresh_token (encrypted), calendar_id, color, is_active
- `chat_messages` — id, user_id, family_id, message, role (user/assistant)
- `point_transactions` — id, family_id, user_id, type (enum), points (int), description, source_type/id (polymorphic), awarded_by
- `rewards` — id, family_id, created_by, title, description, point_cost, icon, is_active, sort_order
- `reward_purchases` — id, family_id, reward_id, user_id, points_spent, purchased_at
- `badges` — id, family_id, created_by, name, description, icon, color, trigger_type, trigger_threshold, is_hidden, is_active
- `user_badges` — id, user_id, badge_id, earned_at, awarded_by (unique: user_id+badge_id)

## Session Rules

**IMPORTANT: Follow these rules in every session working on this project.**

1. **Read this file first.** It's your project briefing.
2. **Check `docs/ROADMAP.md`** to understand what phase we're in and what's next.
3. **Check `CHANGELOG.md`** to see what was done in recent sessions.
4. **Ask Greg what he wants to focus on** before starting work. Don't assume.
5. **After completing work**, update:
   - `CHANGELOG.md` — What was done this session
   - `CLAUDE.md` — If any architectural decisions changed, modules were added/modified, or the tech stack evolved
   - `docs/ROADMAP.md` — If features moved between phases or new ones were added
6. **Don't scope-creep.** Greg has ADHD and will throw out ideas. Capture them in the roadmap but stay focused on what was agreed for the session.
7. **Test your changes** when possible. If you can't run the app, at least verify syntax and logic.
8. **Keep the API-first principle.** Never bypass the API from the frontend.
9. **Mobile-first.** Always design for phone screens first.
10. **Security matters.** This app stores SSNs, medical records, financial info. Treat every feature with that in mind.

## Local Development Setup

**Preferred: Native on Mac (Homebrew)**
```bash
brew install php@8.3 composer postgresql@16 redis
brew services start postgresql@16
brew services start redis
createdb kinhold
cd kinhold
cp .env.example .env
# Edit .env: DB_HOST=127.0.0.1, DB_DATABASE=kinhold, DB_USERNAME=<mac-username>, DB_PASSWORD=
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan db:seed
# Tab 1: npm run dev
# Tab 2: php artisan serve
# Open http://localhost:8000
```

**Alternative: Docker** (Dockerfile fixed, setup.sh included)
```bash
chmod +x setup.sh && ./setup.sh
```

## Current Status (Updated: 2026-04-01)

**Phase:** MVP deployed to production. Phase 0 (Foundations) complete. Gamification, onboarding wizard, MCP server, and profile pictures all shipped. Rebranded to Kinhold. **Pushed to GitHub as public open-source repo.** Security audit complete (PR #110). Self-hosting infrastructure shipped (PR #115). CI pipeline + open-source community docs in place (PR #116).

**Production:** Deployed on Upsun at `kinhold.app` (project ID: `2rozcvqjtjdta`, Terra Nova org). GitHub integration auto-deploys on push to `main`. PRs auto-create preview environments. Never use `upsun push` — just push to GitHub.

**GitHub:** https://github.com/gregqualls/kinhold (public, Elastic License 2.0)

**What works:**
- App boots and runs locally (`php artisan serve` + `npm run dev`)
- Frontend builds clean (791 Vue/JS modules, 0 errors via `npx vite build`)
- Auth flow works (login/register with demo accounts from seeder)
- Task CRUD fully functional: create, edit, complete, delete tasks and task lists
- Calendar view with month/week/day modes, Google Calendar integration, color-coded events with source labels
- Vault with categories, encrypted entries, sensitive field masking
- Chat with message bubbles, typing indicator, suggested questions
- Dark mode fully supported across all views and components
- Custom 5-color palette (Prussian Blue, Wisteria, Lavender, Golden Sand, Black) applied everywhere
- UI/UX overhaul complete for Phases 1, 2, 4, 6 (shared components, tasks, vault, chat)
- **Gamification system:** Points, kudos, leaderboard, rewards store, badges — all wired up
- **Recurring tasks:** RRULE-based with daily artisan command
- **Feature toggles:** Parents can enable/disable modules (calendar, tasks, vault, chat, points, badges)
- **Onboarding wizard:** 5-step setup for parents (welcome, add family, calendar, tags, granular feature access). Simplified flow for joining members with feature explainer. Re-triggerable from Settings.
- **Profile pictures:** Photo upload, 26 Phosphor icon presets (duotone), 12 brand color picker, Google photo restore. `avatar` stores URL/preset key/null. `avatar_color` stores chosen color name. `google_avatar` persists Google OAuth photo. Uploaded files served via `GET /api/v1/user/avatar/{userId}` (not symlink).
- **Laravel-native MCP server:** 18 tools at `/mcp`, Sanctum bearer token auth, direct model access
- **Security hardened:** Full audit completed (Session 13). Cross-family isolation on all policies/controllers, OAuth auth code exchange (not token-in-URL), rate limiting on auth endpoints, CSRF-protected calendar OAuth, SSRF protection on ICS, file type restrictions on vault uploads.
- **Unified policy-based auth (Session 15):** 8 Laravel Policies (Task, TaskList, VaultEntry, Family, Badge, Tag, Reward, FeaturedEvent) serve as single source of truth for both API and MCP layers. `ScopesToFamily::authorize()` delegates to Gate from MCP tools. `Badge::maskHidden()` shared between API and MCP. Foundation for Issue #107 (child access controls) is in place.
- **Google account linking:** Email/password users can link Google from Settings or when attempting Google sign-in (password confirmation flow).
- **Email verification:** Sent on registration, dismissable banner for unverified users. Existing users grandfathered.
- **Self-hosting infrastructure:** Zero-dependency Docker setup with SQLite (`docker-compose.simple.yml` + `setup-simple.sh`). Graceful feature degradation — Google OAuth, Calendar, AI Chat hide/show based on config. Public `/api/v1/config` endpoint for pre-auth service detection. First-boot auto-redirect to register. Comprehensive `SELF-HOSTING.md` guide.
- **GitHub Actions CI:** PHPUnit tests + Vite build run on every PR and push to main. Workflow at `.github/workflows/ci.yml`.
- **Community docs:** CODE_OF_CONDUCT.md, SECURITY.md, PR template, Elastic License 2.0 consistently applied everywhere.
- **45 automated tests:** Security (35), Google link (5), email verification (5). Run with `./vendor/bin/phpunit`. CI runs on SQLite in-memory.

**Dark mode architecture (important for future work):**
- `darkMode: 'class'` in Tailwind config — `<html class="dark">` toggles it
- `useDarkMode` composable handles toggling + localStorage persistence
- Dark mode toggle currently lives in Settings > Appearance only — needs TopBar/mobile toggle
- CSS architecture: dark mode defaults for component classes (`.card`, `.input-base`, `.btn-*`) live in `@layer components` in `app.css`. Explicit `dark:` Tailwind utilities override these when needed. **Do NOT put dark overrides outside `@layer`** — they will beat Tailwind utilities due to CSS cascade rules.
- If dark mode appears visually broken, restart the Vite dev server first — stale Vite processes won't generate custom color utilities

**Known issues:**
- `CalendarEventResource` receives raw arrays (from Google API), not Eloquent models — may need adjustment
- Vault route conflict possible: `/vault/entry/:id` vs `/vault/:categorySlug`
- Google Calendar OAuth flow needs real credentials from Google Cloud Console
- Chatbot needs `ANTHROPIC_API_KEY` in `.env`
- VaultEncryptionService uses a simple encrypt/decrypt — verify round-trip works on first real entry
- Vite dev server can go stale with high CPU — kill and restart if CSS isn't generating correctly

**What's next:**
- Versioning, GitHub Releases, and self-hosted update notifications (Issue #117) — close to needing this
- Audit all controllers for family_id scoping before Corey's family signs up
- Add dark mode toggle to TopBar (desktop) and mobile header for quick access
- End-to-end testing of gamification flow (complete task → points → badge earned → toast)
- Test recurring task generation: `php artisan app:generate-recurring-tasks`
- Continue UI/UX overhaul: Phase 3 (Calendar components) and Phase 5 (Dashboard enhancements)
- See `docs/ROADMAP.md` for full phased approach

## Deployment Strategy (Upsun)

**Problem:** Greg owns the open-source repo (`gregqualls/kinhold`) and also wants to deploy a personal instance for his family. Other users should be able to fork/deploy their own instance and pull upstream updates.

**Solution: Single repo, Upsun connects directly to `main` branch.**

- **No fork needed.** Greg owns the repo. Upsun connects directly to `gregqualls/kinhold`.
- The `.upsun/config.yaml` is already in the repo (committed in Session 1).
- Family-specific config (API keys, DB creds, domain) lives in Upsun environment variables — never in the repo.
- Other users fork the repo, connect their fork to their own Upsun project (or any host), and pull upstream updates with `git pull upstream main`.

**Deployment steps (already complete):**
1. Upsun project created and connected to GitHub repo (`gregqualls/kinhold`, `main` branch)
2. Environment variables set on Upsun (APP_KEY, DB creds, Redis, Google OAuth, Anthropic key)
3. `.upsun/config.yaml` has build/deploy hooks (composer install, npm build, migrate, etc.)
4. Domain configured: kinhold.app (via Cloudflare)
5. SSL handled automatically by Upsun
6. PRs auto-create preview environments

**For other users who want to deploy:**
1. Fork `gregqualls/kinhold` on GitHub
2. Connect their fork to their Upsun project (or Docker/VPS/whatever)
3. Set their own environment variables
4. To get updates: `git remote add upstream https://github.com/gregqualls/kinhold && git pull upstream main`

## Aspirational Features (Not Planned Yet)

These are Greg's ideas. Don't build them unless Greg specifically asks. Capture new ideas here.

- ~~Gamification: Points on tasks, badges/achievements, leaderboard~~ — IMPLEMENTED (Session 5)
- Real-money rewards for kids ($ values on tasks, track earnings)
- Family budget and expense tracking with allowances
- Meal planning, recipe storage, grocery lists
- Family chat/messaging with polls
- Activity feed and notifications (push + email)
- Mobile app (React Native or PWA)
- Two-factor authentication
- More calendar providers (Outlook, iCloud)
- Shared-with-external for vault (e.g., share medical info with doctor)
- Family photo gallery / memories timeline
- Chore rotation system
- School schedule integration
- Emergency contacts and info page
