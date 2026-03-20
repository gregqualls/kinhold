# Q32 Hub — Changelog

> Updated at the end of every working session. Newest entries first.

## 2026-03-17 — Session 7: Upsun Deployment & Google OAuth

### What Was Done
- **Deployed Q32 Hub to Upsun** at `family.qthirtytwo.com`
  - Created project in Terra Nova org (project ID: `2rozcvqjtjdta`)
  - Connected to GitHub repo — pushes to `main` auto-deploy
  - Iterated on `.upsun/config.yaml` (php:8.4, n version manager, pdo_pgsql, Redis, storage mounts)
  - Created `.environment` file to map `PLATFORM_RELATIONSHIPS` to Laravel env vars
  - Set all production environment variables (APP_KEY, DB, Redis, session, Google OAuth, etc.)
  - Fixed multiple deployment issues: PHP version, bootstrap/cache permissions, POSIX shell compat, pdo_pgsql extension, storage:link on read-only fs

- **Created Greg's admin account on production** via SSH

- **Added Google OAuth login (Laravel Socialite)**
  - New `GoogleAuthController` with redirect + callback (3 cases: existing google_id, existing email, new user+family)
  - `config/services.php` with separate `GOOGLE_AUTH_REDIRECT_URI` for auth (vs calendar)
  - Migration: added `google_id` to users, made `password` nullable
  - Frontend: "Sign in with Google" / "Sign up with Google" buttons on LoginView + RegisterView
  - `auth.js` store: `initAuth()` picks up `?token=` from OAuth callback URL
  - Routes in `web.php` for `/auth/google/redirect` and `/auth/google/callback`

- **Fixed production bugs:**
  - CSRF token mismatch — set `SESSION_SECURE_COOKIE=true` and `SESSION_DOMAIN` on Upsun
  - Missing sessions table — created migration with `foreignUuid` (not `foreignId`)
  - Sessions table `user_id` type mismatch (bigint vs UUID) — fix migration for production
  - Settings 500 error — double-encoded JSON in family settings, fixed data on production
  - No logout button — added Sign Out button to `Sidebar.vue`
  - Google OAuth "missing client_id" — set `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_AUTH_REDIRECT_URI` on Upsun

### Files Created
- `.environment` — Upsun platform relationship mapping
- `app/Http/Controllers/Api/V1/GoogleAuthController.php`
- `config/services.php`
- `database/migrations/2026_03_17_183542_create_sessions_table.php`
- `database/migrations/2026_03_17_184500_fix_sessions_user_id_to_uuid.php`
- `database/migrations/2026_03_17_185421_add_google_id_to_users_table.php`

### Files Modified
- `.upsun/config.yaml` — Rewrote for working Upsun deployment
- `app/Models/User.php` — Added `google_id` to fillable
- `resources/js/views/auth/LoginView.vue` — Google OAuth button
- `resources/js/views/auth/RegisterView.vue` — Google OAuth button
- `resources/js/stores/auth.js` — OAuth token pickup from URL
- `resources/js/components/layout/Sidebar.vue` — Sign Out button
- `routes/web.php` — Google OAuth routes
- `composer.json` — Added `laravel/socialite`

### Architecture Decisions
- Google OAuth callback redirects to `/login?token=xxx` for SPA to pick up via Sanctum token
- Separate `GOOGLE_AUTH_REDIRECT_URI` env var from calendar's `GOOGLE_REDIRECT_URI`
- Multi-family data isolation already supported (all tables scoped by `family_id`)

### Next Session TODO
- Verify Google OAuth works end-to-end on production (requires adding redirect URI in Google Cloud Console)
- Audit all controllers for family_id scoping before Corey signs up
- Dark mode toggle in TopBar
- End-to-end testing of gamification flow
- Continue UI/UX overhaul: Calendar, Dashboard

---

## 2026-03-17 — Session 6: Open Source Release & GitHub Push

### What Was Done
- **Verified parent-only access controls** — confirmed all sensitive UI buttons (badge creation, point deduction, reward management) are properly gated with `v-if="isParent"` across BadgesView, PointsFeedView, RewardsView, RewardCard, VaultCategoriesView, VaultEntriesView, DashboardView
- **Captured 4 dark mode screenshots** for README using Playwright headless Chromium — points feed, badges, rewards, tasks (saved to `docs/screenshots/`)
- **Rewrote README.md** for open-source release — professional formatting with features, screenshots, tech stack, quick start (native + Docker), demo accounts, API routes, MCP server docs, contributing guide, roadmap link
- **Expanded `.env.example`** — full template with all config vars, no secrets
- **Updated `.gitignore`** — added vendor, Laravel cache/session/view paths, .claude/, session captures, test-results
- **Created initial git commit** — 207 files, 31,838 insertions
- **Pushed to GitHub** — public repo at https://github.com/gregqualls/q32hub
  - `gh repo create q32hub --public --source . --push`

### Next Session TODO
- Deploy to Upsun for personal/family use (plan documented below in Session 6 notes)
- Dark mode toggle in TopBar (still pending)
- End-to-end testing of gamification flow
- Test recurring task generation
- Continue UI/UX overhaul: Calendar components, Dashboard enhancements

---

## 2026-03-17 — Session 5: Gamification System (Points, Rewards, Badges)

### What Was Done
- **Full gamification system implemented** across ~50 new/modified files covering backend, frontend, and integration.

- **Backend — Migrations (6 new):**
  - `add_recurrence_to_tasks_table` — points, recurrence_rule, recurrence_end, parent_task_id
  - `create_point_transactions_table` — ledger-based points system with polymorphic source
  - `create_rewards_table` — parent-created prizes purchasable with points
  - `create_reward_purchases_table` — purchase history
  - `create_badges_table` — Steam-style achievements with trigger types
  - `create_user_badges_table` — pivot with earned_at and awarded_by

- **Backend — Enums (2 new):**
  - `PointTransactionType` — task_completion, task_reversal, kudos, deduction, redemption, adjustment
  - `BadgeTriggerType` — points_earned, tasks_completed, task_streak, kudos_received/given, rewards_purchased, login_streak, custom

- **Backend — Models (4 new, 3 updated):**
  - New: PointTransaction, Reward, RewardPurchase, Badge
  - Updated: Task (points, recurrence, getEffectivePoints), User (pointBank, badges), Family (leaderboard period, enabled modules)

- **Backend — Services (2 new):**
  - `PointsService` — award/reverse task points, kudos, deductions, reward redemption, leaderboard with period-scoped rankings
  - `BadgeService` — auto-checks badge thresholds after events, manual award/revoke, streak calculations

- **Backend — Controllers (3 new, 1 updated):**
  - New: PointsController (bank, leaderboard, feed, kudos, deduct), RewardsController (CRUD + purchase), BadgesController (CRUD + award/revoke + progress)
  - Updated: TaskController — awards points on complete, reverses on uncomplete, checks badges

- **Backend — Recurring Tasks:**
  - `GenerateRecurringTasks` artisan command — parses RRULE (DAILY, WEEKLY+BYDAY, MONTHLY+BYMONTHDAY), generates 7 days ahead
  - Scheduled daily at 00:05 via Kernel

- **Backend — Feature Toggles:**
  - SettingsController accepts enabled_modules + leaderboard_period
  - Stored in families.settings JSON column

- **Frontend — Pinia Stores (2 new, 1 updated):**
  - `stores/points.js` — bank, leaderboard, feed, rewards, purchases with all CRUD actions
  - `stores/badges.js` — badges, earned badges with CRUD + award/revoke
  - `stores/auth.js` — added enabledModules and isModuleEnabled computed

- **Frontend — Points Views & Components (3 views, 6 components):**
  - PointsFeedView — balance card, leaderboard strip, scrollable activity feed, kudos input, deduct modal
  - RewardsView — reward grid with purchase flow, parent CRUD
  - PointsHistoryView — personal transaction history
  - Components: LeaderboardStrip, FeedItem, KudosInput, DeductModal, RewardCard, TaskPointsBadge

- **Frontend — Badges Views & Components (1 view, 5 components):**
  - BadgesView — All/Earned/Locked tabs, create badge form, icon picker, manual award
  - Components: BadgeIcon (hexagonal SVG), BadgeCard, BadgeShowcase, BadgeProgressBar, badgeIcons.js (20 SVG paths)

- **Frontend — Integration:**
  - Sidebar + BottomNav — Points and Badges nav items, filtered by enabled modules
  - TopBar — page titles for all new views
  - DashboardView — Points balance card + LeaderboardStrip, Badges showcase
  - Router — /points, /points/rewards, /points/history, /badges with module guards
  - SettingsView — module toggles for points/badges, leaderboard period selector

- **Seeder — Demo Data:**
  - 5 rewards (Sweets 10pts, TV Time 20pts, Pick Dinner 30pts, Movie Pick 40pts, Stay Up Late 75pts)
  - Tasks with point values + recurring "Take out trash" every Tuesday
  - 7 point transactions (Demo Child has 45 pts in bank)
  - 11 badges (10 preset + 1 custom "Welcome"), 2 earned by Demo Child
  - Family settings with all modules enabled + weekly leaderboard

### Architecture Decisions
- **Ledger pattern** for points: Point Bank = SUM(all transactions), Leaderboard = SUM(positive in current period). Never mutate a running balance — always append transactions.
- **Instant purchases** — no approval flow for rewards. Points deducted immediately.
- **Hidden badges** show as "???" until earned — fun surprise mechanic for kids.
- **Feature toggles** stored in family settings JSON, enforced at nav/router/API level.

### Build Status
- 791 Vue/JS modules, 0 errors via `npx vite build`
- All 17 migrations run successfully
- Seeder creates full demo data (verified: 2 users, 5 tasks, 5 rewards, 11 badges, 7 transactions, 2 earned badges, child bank = 45 pts)

### Next Session TODO
- Dark mode toggle in TopBar (still pending from Session 4)
- Test the full flow end-to-end in browser (complete task → points awarded → badge earned → toast)
- Test recurring task generation: `php artisan app:generate-recurring-tasks`
- Test feature toggles: disable points/badges → verify nav/routes hidden

---

## 2026-03-17 — Session 4: Dark Mode & CSS Architecture Fix

### What Was Done
- **Fixed dark mode CSS architecture:**
  - Root cause: global dark mode overrides in `app.css` were outside `@layer`, making them always beat Tailwind's `dark:` utilities (which live inside `@layer utilities`). This meant all explicit `dark:` classes were being silently ignored.
  - Moved dark mode overrides into `@layer components` so they serve as defaults that Tailwind's `dark:` utilities can properly override.
  - Removed blunt global overrides (`.dark .bg-white`, `.dark .text-prussian-500`, `.dark .bg-lavender-50`, `.dark .border-lavender-200`, etc.) that were masking everything.
  - Kept component-level dark overrides (`.dark .card`, `.dark .input-base`, `.dark .btn-secondary`, `.dark .btn-ghost`, `.dark .divider`) as sensible defaults in `@layer components`.

- **Added dark mode variants to SettingsView.vue:**
  - All 6 section headings now have `dark:text-lavender-200`
  - All labels, descriptions, helper text have `dark:text-lavender-300` or `dark:text-lavender-400`
  - All list items (`bg-lavender-50`) have `dark:bg-prussian-700`
  - All borders have `dark:border-prussian-700`
  - Error/info banners have dark variants (`dark:bg-red-900/20`, `dark:bg-blue-900/20`)

- **Improved task save button UX (TaskDetailPanel.vue):**
  - Added dirty state tracking (`isDirty` computed) that compares form values against original snapshot
  - "Unsaved changes" label (gold text) appears when any field is modified
  - Save button gets subtle glow + scale-up when dirty to draw attention
  - "Saved!" confirmation with green checkmark flashes for 2 seconds after saving
  - Fixed invalid Tailwind class `dark:bg-prussian-700.5` (appeared on date and select inputs)

- **Added calendar source labels (CalendarView.vue):**
  - Added `calendarNameMap` computed and `getCalendarSourceName()` helper
  - Month view: tooltip on event chips shows calendar source
  - Week view: small text line under each event shows source name
  - Day view: colored dot + label shows calendar source

- **Dark mode fixes across remaining components:**
  - DashboardView: Quick Actions heading, task text, completed task styles
  - TaskItem: due date classes returned from JS now include `dark:` variants
  - TopBar: avatar ring color (`dark:ring-prussian-800`)

- **Discovered stale Vite dev server issue:**
  - The Vite dev server had been running since session start, consuming 2245 min CPU, and was NOT generating custom color Tailwind utilities
  - Restarting it fixed CSS generation — all `dark:bg-prussian-*`, `dark:text-lavender-*` etc. now properly generated
  - Important: if dark mode appears broken, restart the Vite dev server first (`kill` the old process, then `npm run dev`)

### Build Status
- 774 Vue/JS modules, 0 errors via `npx vite build`
- Dark mode verified working in browser — cards, headings, borders, inputs all correct
- Light mode verified still working correctly

### Next Session TODO
- **Add dark mode toggle to TopBar** (desktop) and mobile header — currently only accessible via Settings > Appearance
- **Update ROADMAP.md** — Dark mode status should change from DEFERRED to IN PROGRESS/COMPLETE
- Continue with Phase 3 (Calendar) or Phase 5 (Dashboard) from the UI/UX overhaul plan

---

## 2026-03-16 — Session 3: UI/UX Overhaul (Phases 1-2-4-6)

### What Was Done
- **Phase 1 — Shared UI Components:**
  - New `ConfirmDialog.vue` — Destructive action confirmation with loading state
  - New `ContextMenu.vue` — Dropdown menus with actions, dividers, danger variants
  - New `SlidePanel.vue` — Right-side slide-over panel for detail editing
  - New `FloatingActionButton.vue` — Mobile FAB for primary create actions
  - New `UndoToast.vue` — Undo-able toast notifications with auto-dismiss
  - Updated `UserAvatar.vue` — Added `xs` size for inline use
  - Updated `App.vue` — Page transitions, polished toast notifications, removed stale auth loading overlay
  - Updated `Sidebar.vue` — Q logo, cleaner nav with active states, user role display
  - Updated `BottomNav.vue` — Solid/outline icon switching for active tab, frosted glass background
  - Updated `TopBar.vue` — Simplified, overlapping avatar stack
  - New CSS animations — scale transitions, checkbox bounce, task list transitions

- **Phase 2 — Tasks (Todoist-inspired):**
  - Complete rewrite of `TaskListsView.vue`:
    - Today / Upcoming smart view cards
    - Task list rows with colored icons, progress rings, task counts
    - Context menu on each list (Edit / Delete)
    - Create + Edit list modals with color picker
    - Delete confirmation dialog
    - Mobile FAB + desktop "New List" button
  - Complete rewrite of `TaskListDetailView.vue`:
    - Inline quick-add input (Task name + Date + Priority cycling)
    - Animated circle checkboxes colored by priority (red=high, orange=medium, gray=low)
    - All/Priority filter tabs with live counts
    - Task detail slide panel (edit title, description, priority, due date, assignee, completion toggle)
    - Delete task confirmation
    - Undo toast on task completion
    - Edit/Delete list from within the detail view
    - Collapsible completed tasks section
  - New task components: `TaskCheckbox.vue`, `TaskItem.vue`, `TaskQuickAdd.vue`, `TaskDetailPanel.vue`

- **Phase 4 — Vault (1Password-inspired):**
  - Rewrite of `VaultCategoriesView.vue` — Search bar, category cards with colored icons, "Add Entry" modal with dynamic key-value fields
  - Rewrite of `VaultEntriesView.vue` — Search, entry list with context menus, delete confirmation
  - Rewrite of `VaultEntryView.vue` — Data fields with SensitiveField component, documents, permissions, metadata
  - New `SensitiveField.vue` — Eye toggle reveal, one-click copy with auto-clear clipboard (30s), auto-hide on tab blur

- **Phase 6 — Chat (Polish):**
  - Rewrite of `ChatView.vue` — Message bubbles (user=right/blue, AI=left/gray), animated typing indicator (bouncing dots), suggested question cards, fixed bottom input bar

- **Bug Fixes:**
  - Fixed auth `isLoading` overlay staying visible during background `fetchUser()` calls
  - Fixed `createTask` using wrong API endpoint (`POST /tasks` → `POST /task-lists/{id}/tasks`)
  - Fixed `toggleComplete` using wrong endpoint (`/toggle-complete` → `/complete` or `/uncomplete`)
  - Fixed `fetchTasks` not loading `taskLists` when navigating directly to a task list detail page
  - Fixed `currentList` not resolving when `taskLists` array was empty on direct navigation

### Build Status
- 772 Vue/JS modules, 0 errors via `npx vite build`
- All pages verified in browser (mobile + desktop viewports)
- Task CRUD fully functional: create, edit, complete, delete tasks and task lists

## 2026-03-16 — Session 1: Project Scaffolding

### What Was Done
- **Project kickoff:** 5 rounds of design questions to nail down MVP scope, tech stack, and architecture
- **Full project scaffolding:** 146 files across backend, frontend, MCP server, and infrastructure
- **Backend (Laravel 11):**
  - 9 Eloquent models with full relationships (User, Family, Task, TaskList, VaultEntry, VaultCategory, VaultPermission, Document, CalendarConnection)
  - 3 backed enums (FamilyRole, TaskPriority, PermissionLevel)
  - 10 database migrations with proper foreign keys and indexes
  - 9 API controllers with CRUD operations
  - 6 form request validators
  - 8 API resource transformers
  - 4 authorization policies
  - 3 service classes (GoogleCalendar, VaultEncryption, Chatbot)
  - 2 database seeders (vault categories + demo family)
  - Full route configuration with Sanctum middleware
- **Frontend (Vue 3 SPA):**
  - 5 Pinia stores (auth, tasks, vault, calendar, chat)
  - Vue Router with auth guards (guest, authenticated, parent-only)
  - 7 common components (BaseCard, BaseButton, BaseModal, BaseInput, UserAvatar, EmptyState, LoadingSpinner)
  - 3 layout components (BottomNav, Sidebar, TopBar)
  - 9 page views (Login, Register, Dashboard, Calendar, TaskLists, TaskDetail, VaultCategories, VaultEntries, VaultEntry, Chat, Settings)
  - Module-specific components for calendar, tasks, vault, and chat
  - API service with Axios (CSRF, auth, error handling)
  - 2 composables (useNotification, useFamilyColors)
  - Tailwind CSS with custom warm color palette
- **MCP Server:**
  - TypeScript with @modelcontextprotocol/sdk
  - 26 tools across 5 categories (tasks, calendar, vault, family, chat)
  - API client with Sanctum token auth
- **Infrastructure:**
  - Docker Compose with app, nginx, PostgreSQL, Redis, node services
  - Multi-stage Dockerfile
  - Nginx configuration
  - Upsun deployment config (`.upsun/config.yaml`)
  - Setup script (`setup.sh`)
- **Documentation:**
  - CLAUDE.md (project context for future sessions)
  - docs/ARCHITECTURE.md (technical decisions with reasoning)
  - docs/ROADMAP.md (4-phase feature plan)
  - docs/CONVENTIONS.md (coding standards)
  - CHANGELOG.md (this file)
  - README.md (open-source project readme)
  - MIT LICENSE

### Decisions Made
- Laravel 11 REST API + Vue 3 SPA (not Livewire/Inertia)
- PostgreSQL over MySQL (better JSON, full-text search)
- App-level encryption for vault (not per-user or zero-knowledge — enables chatbot)
- Hybrid vault permissions (parent/child roles + per-item overrides)
- MCP server in TypeScript (better SDK support)
- Mobile-first card-based UI with bottom navigation
- Cookie auth for SPA, token auth for MCP
- Open source: MIT license, Docker + self-hosted deployment

### Bug Fixes Applied (same session)
- Fixed CSS import path in app.js (`@/css/app.css` → `../css/app.css`)
- Fixed `creator_id` → `created_by` in TaskController, TaskListController, VaultController, TaskPolicy
- Fixed AuthController to use direct `family_id` assignment instead of non-existent pivot table
- Added `currentFamily()` query builder method to User model
- Created missing ChatMessage model + migration
- Fixed ChatbotService to use HTTP client instead of non-existent Anthropic PHP SDK
- Removed non-existent CalendarEvent model reference from ChatbotService
- Added `invite_code` column to families migration and Family model fillable
- Fixed CalendarController field names (`color_code` → `color`, removed `calendar_email`)
- Fixed Document creation in VaultController to use polymorphic fields correctly
- Fixed Dockerfile (`vite.config.ts` → `vite.config.js`, added `php` stage name, added `icu-dev`)
- Improved setup.sh with better error handling and Docker Compose v2 support
- Simplified entrypoint.sh (removed non-existent artisan commands)
- Frontend builds clean: 431 modules, 0 errors

### Known Issues / Next Steps
- [ ] Need Docker on local machine to boot (`chmod +x setup.sh && ./setup.sh`)
- [ ] Google Calendar OAuth needs real credentials from Google Cloud Console
- [ ] Chatbot needs Anthropic API key in `.env`
- [ ] Route conflict possible: `/vault/entry/:id` vs `/vault/:categorySlug` — needs runtime testing
- [ ] Some Vue components reference `@heroicons/vue` which may need icon adjustments
- [ ] `CalendarEventResource` receives arrays not models — may need adjustment
- [ ] Vault encryption service needs testing with actual encrypted data round-trips
