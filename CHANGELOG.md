# Kinhold — Changelog

> Updated at the end of every working session. Newest entries first.

## 2026-03-31 — Session 15: Unified Policy-Based Auth for MCP + API (#98)

### What Was Done
- **4 new Laravel Policies created** — `BadgePolicy`, `TagPolicy`, `RewardPolicy`, `FeaturedEventPolicy` — each enforcing parent-only write access as the single source of truth for both API and MCP layers.
- **`authorize()` helper added to `ScopesToFamily` trait** — MCP tools can now delegate to Laravel Gate/policies via `$this->authorize($ability, $model)`, returning a structured error response if denied.
- **`Badge::maskHidden()` static method** — Shared presentation logic extracted to the model. Web UI hides from all users (surprise mechanic preserved); MCP shows parents full badge details (management interface).
- **8 MCP tools migrated** — `ManageBadges`, `ManageTags`, `ManageRewards`, `ManageFeaturedEvents`, `ManageTasks`, `ManageVault`, `ManageVaultAccess`, `CompleteTask` all replaced inline `requireParent()` / ad-hoc checks with policy-based `$this->authorize()` calls.
- **4 API controllers migrated** — `TagController`, `RewardsController`, `BadgesController`, `FeaturedEventController` replaced remaining inline `isParent()` checks with `$this->authorize()` policy calls.
- **4 new security tests** — `test_child_cannot_create_tag`, `test_child_cannot_delete_tag`, `test_child_sees_masked_hidden_badges`, `test_parent_sees_masked_hidden_badges_in_web_ui`. Total: 45 tests, all passing.
- **MCP-first guardrails principle established** — Authorization for any module now lives in one policy file; both API and MCP inherit changes automatically. Foundation laid for Issue #107 (child access controls).

### Files Modified
- New: `app/Policies/BadgePolicy.php`, `TagPolicy.php`, `RewardPolicy.php`, `FeaturedEventPolicy.php`
- Modified: `app/Mcp/Tools/Concerns/ScopesToFamily.php`, `app/Models/Badge.php`
- Modified MCP tools: `ManageBadges.php`, `ManageTags.php`, `ManageRewards.php`, `ManageFeaturedEvents.php`, `ManageTasks.php`, `ManageVault.php`, `ManageVaultAccess.php`, `CompleteTask.php`
- Modified controllers: `TagController.php`, `RewardsController.php`, `BadgesController.php`, `FeaturedEventController.php`
- Modified: `tests/Feature/SecurityTest.php` (4 new tests)

### PR
- #114 — `fix/98-mcp-policy-auth` (merged)

---

## 2026-03-31 — Session 14: Self-Hosting Accessibility Planning

### What Was Done
- **Self-hosting accessibility research** — Analyzed n8n's open-source model (licensing, Docker setup, feature gating strategy) and mapped it to Kinhold's current external dependencies.
- **Dependency audit** — Cataloged all external service requirements (PostgreSQL, Redis, SMTP, Google OAuth, Anthropic API) and identified which are truly required vs optional.
- **3-sprint implementation plan** — Documented at `.claude/plans/self-hosting-accessibility.md`:
  1. Zero-Config First Run: SQLite default, `docker-compose.simple.yml` (2 services), auto APP_KEY
  2. Graceful Feature Degradation: runtime feature detection, `/api/v1/config` endpoint, conditional UI
  3. DX & Polish: first-boot setup wizard, `SELF-HOSTING.md`, updated README
- **New architecture principle** — Added #5 to CLAUDE.md: "Self-hostable by default — We don't gate features — we gate operational complexity."
- **GitHub issue #113** — Created with full sprint checklists for tracking.

### Files Modified
- `CLAUDE.md` — Added architecture principle #5 (self-hostable by default)

### No PR
- Planning session only, no code changes to ship.

---

## 2026-03-29 — Session 13: Security Audit + Google Linking + Email Verification

### What Was Done
- **Comprehensive security audit** — Found and fixed 22 vulnerabilities (3 Critical, 6 High, 8 Medium, 5 Low). Full details in PR #110.
  - **Critical:** Cross-family data isolation (vault SSNs, tasks, rewards, badges accessible across families), OAuth token leaked in URL, no login rate limiting
  - **High:** Google OAuth account takeover via email auto-linking, Calendar OAuth CSRF (unsigned state), SSRF via ICS subscription, vault accepted any file type
  - **Medium:** Self-selecting parent role on invite join, weak passwords (only min:8), short invite codes, error messages leaking internals, cross-family validation gaps
- **Google account linking** — Users who registered with email/password can now link Google from Settings. When trying Google sign-in with an existing account, they're prompted for their password to confirm the link (instead of being rejected).
- **Email verification on registration** — New users get a verification email. Dismissable amber banner in the app until verified. Resend endpoint throttled to 3/min. Existing users grandfathered.
- **41 automated tests** — 31 security tests + 5 Google link tests + 5 email verification tests. Model factories created (FamilyFactory, UserFactory).

### Files Modified
- 6 controllers (Auth, Google Auth, Badges, Rewards, Calendar, Chat, Vault)
- 2 policies (VaultEntryPolicy, TaskPolicy) — added family_id checks to all methods
- 4 form requests (RegisterRequest, StoreTaskRequest, StoreVaultEntryRequest, GrantPermissionRequest)
- User model (MustVerifyEmail, guarded fields), UserResource (google_id boolean, email_verified_at)
- IcsCalendarService (SSRF protection), routes/api.php (rate limiting, new endpoints), routes/web.php (verification, link callback)
- SPA: auth store (code exchange, pending link, resend verification), LoginView (link confirmation UI), SettingsView (Google link/unlink), App.vue (verification banner)
- New: PendingLinkException, FamilyFactory, UserFactory, SecurityTest, GoogleLinkTest, EmailVerificationTest

### PR
- #110 — `security/audit-and-fixes` (pending merge)

---

## 2026-03-29 — Session 12: AI Chat Activation + OAuth MCP Connector

### What Was Done
- **Laravel Passport OAuth 2.0 for MCP** — Claude Desktop can now connect with just the URL `https://kinhold.app/mcp`, no token copy-paste needed. Google OAuth popup → approve → connected.
  - Installed `laravel/passport`, configured `api` guard, added `Mcp::oauthRoutes()`
  - Added session-based Google OAuth login route (`/login` → `/auth/google/oauth-callback`) for Passport's consent screen
  - Published and customized MCP authorize view (`resources/views/mcp/authorize.blade.php`)
  - PASSPORT_PRIVATE_KEY / PASSPORT_PUBLIC_KEY set on Upsun via REST API (CLI couldn't parse PEM)
  - SPA catch-all regex updated to not swallow `/oauth/` and `/.well-known/` routes
- **Email notifications fixed** — Resend was being overridden by Upsun's platform SMTP injection. Disabled via `upsun environment:info enable_smtp false`. Confirmed delivery working.
- **AI Chat activated** — Two-tab UI in Settings: "Use Kinhold AI" (platform key) vs "My Own API Key" (BYOK). `ai_mode` field added to family settings.
  - `ChatbotService::resolveProvider()` respects `ai_mode` — kinhold mode uses `ANTHROPIC_API_KEY` env var, byok uses encrypted family key
  - Fixed missing AI & Integrations section: `window.location.origin` in Vue template caused silent TypeError that dropped the entire `<SettingsSection>` — moved to `const appOrigin` in script setup
  - Fixed chat gate: `ChatView.vue` now checks `ai_mode === 'kinhold'` OR `ai_has_key` (was only checking BYOK key)
  - Fixed message display: API returns `{role, message}` but template expected `{sender, text}` — normalized in chat store
  - Fixed model: API key account only has Claude 4.x models. `claude-3-5-sonnet-20241022` returns 404. Correct model is `claude-sonnet-4-5-20250929` (verified via models endpoint)
- **4 GitHub issues created** for chat roadmap: #106 (expand context), #107 (child safety), #108 (hidden badge spoilers), #109 (stateless messages)

### Files Modified
- `composer.json` + 5 Passport migrations
- `config/auth.php` — added Passport `api` guard
- `app/Providers/AppServiceProvider.php` — Passport token expiry + auth view
- `routes/ai.php` — `Mcp::oauthRoutes()` + `auth:api,sanctum` middleware
- `routes/web.php` — OAuth login + callback routes, fixed SPA catch-all regex
- `app/Http/Controllers/Api/V1/GoogleAuthController.php` — `oauthLogin()` + `oauthCallback()` for session flow
- `resources/views/mcp/authorize.blade.php` — OAuth consent screen (published + customized)
- `config/services.php` — standardized `RESEND_API_KEY`, default Anthropic model
- `config/kinhold.php` — default Anthropic model
- `.env.example` — updated mail section
- `app/Services/ChatbotService.php` — `ai_mode` awareness in `resolveProvider()`
- `app/Http/Controllers/Api/V1/SettingsController.php` — `ai_mode` in GET/PUT response + validation
- `resources/js/views/settings/SettingsView.vue` — two-tab AI mode UI, `appOrigin` fix
- `resources/js/views/chat/ChatView.vue` — chat gate fix
- `resources/js/stores/chat.js` — normalize `{role,message}` → `{sender,text}`

---

## 2026-03-28 — Session 11: Settings Page Reorganization

### What Was Done
- **Settings page reorganized** into 6 collapsible sections (parent view) for better UX
  - Family, Tasks & Points, AI & Integrations, Feature Access, Appearance, Notifications
  - All sections start collapsed — click to expand what you need
  - Related settings grouped together (task points + task assignment + task access now in one section)
  - AI config + MCP token + calendar connections combined into "AI & Integrations"
  - Setup wizard relocated into the Family section
  - Tasks & Points consolidated to a single "Save Changes" button (was 3 separate saves)
- **Avatar permissions moved into Feature Access** — uses same Everyone/Parents Only/Off/Custom controls as other modules (was a standalone toggle in its own section)
- **Created `ToggleSwitch.vue`** reusable component — standardizes all toggle switches
  - Fixed avatar toggle inconsistency (was gold/smaller, now matches wisteria/standard size)
  - Proper ARIA `role="switch"` and `aria-checked` on all toggles
  - Supports `#thumb` slot for custom content (dark mode icons)
- **Created `SettingsSection.vue`** collapsible card component
  - Icon + title + description header with chevron indicator
  - `v-show` body preserves reactive form state when collapsed
  - URL hash deep-linking (e.g., `/settings#ai-integrations`)
  - Toned-down dark mode hover state
- **Fixed avatar bug** — parents editing a child's avatar would save to their own account instead
  - Backend now accepts `user_id` param on all avatar endpoints, verifies parent+same-family
  - Frontend passes `targetUser.id` in all AvatarEditor API calls
- **Created `docs/SETTINGS.md`** — documents storage map, component APIs, and how to add new settings
- Child view unchanged (stays flat — too few items for collapsible sections)

### Files Created
- `resources/js/components/common/ToggleSwitch.vue`
- `resources/js/components/settings/SettingsSection.vue`
- `docs/SETTINGS.md`

### Files Modified
- `resources/js/views/settings/SettingsView.vue` — full template restructure into collapsible sections
- `app/Http/Controllers/Api/V1/AuthController.php` — avatar target resolution for parent→child edits
- `resources/js/components/common/AvatarEditor.vue` — passes user_id in all API calls

---

## 2026-03-28 — Session 10: Profile Pictures & Avatars

### What Was Done
- **Profile pictures feature** (issue #18, PR #94) — full avatar management system
  - Photo upload via controller-served route (works on Upsun mounts)
  - 26 Phosphor icon presets across 5 categories (Animals, Nature, Space, Style, Vibes) — premium duotone weight
  - 12 brand-approved color picker from the design guide
  - Initials fallback with `@error` handling for broken images
  - `AvatarEditor.vue` modal: color picker, upload, preset grid, "Use Google Photo" restore, remove
  - `children_can_change_avatar` family setting (parent toggle)
- **Installed `@phosphor-icons/vue`** (MIT, tree-shakeable) — also unlocks richer badge icons later
- **Expanded `useFamilyColors`** to all 12 brand colors with user-selectable `avatar_color` column
- **Google avatar persistence** — `google_avatar` column stores Google photo URL permanently, refreshed on every OAuth login, "Use Google Photo" button in editor
- **Closed Phase 0 milestone** on GitHub (was 11/11 but still marked open)
- **Closed #91** — duplicate tag prevention already fixed in `edf099f`

### Files Created
- `resources/js/components/common/AvatarEditor.vue`
- `resources/js/components/common/avatarPresets.js`
- `database/migrations/2026_03_28_203832_add_avatar_color_to_users_table.php`
- `database/migrations/2026_03_28_211116_add_google_avatar_to_users_table.php`
- `public/.user.ini`

### Files Modified
- `app/Http/Controllers/Api/V1/AuthController.php` — 5 new methods (upload, delete, preset, serve, restoreGoogle) + helpers
- `app/Http/Controllers/Api/V1/GoogleAuthController.php` — saves google_avatar on all login paths
- `app/Http/Controllers/Api/V1/SettingsController.php` — children_can_change_avatar setting
- `app/Http/Resources/UserResource.php` — exposes avatar_color, google_avatar
- `app/Models/User.php` — avatar_color, google_avatar fillable
- `resources/js/components/common/UserAvatar.vue` — image/preset/initials priority chain with error fallback
- `resources/js/composables/useFamilyColors.js` — all 12 brand colors, user choice support
- `resources/js/stores/auth.js` — updateUserAvatar helper
- `resources/js/views/settings/SettingsView.vue` — avatar editor integration, parent toggle
- `routes/api.php` — 5 new avatar routes
- `.upsun/config.yaml` — storage:link in deploy hook
- `package.json` — @phosphor-icons/vue dependency

---

## 2026-03-28 — Session 9: Onboarding Wizard

### What Was Done
- **Built onboarding wizard** (issue #63) — 5-step guided setup for new families
  - Welcome (family name, timezone), Add Family (inline member creation), Calendar (Google OAuth), Tags (preset tag creation), Features (granular module access controls)
  - Simplified 3-step flow for joining members: Welcome → Calendar → Feature Explainer → Done
  - Feature explainer shows accessible features with descriptions, locked features greyed out
  - Router guard auto-redirects new users; existing users backfilled to skip
  - Re-triggerable from Settings > "Re-run Setup Wizard"
- **Closed Phase 0: Foundations milestone** — all 11 issues complete (100%)
  - Also closed #76 (Claude connector) — completed in Session 8 but left open
- **Created issue #89** — Remove task_list_id tech debt (tags-only organization)
- **Added `PATCH /api/v1/user`** endpoint for profile updates (timezone)
- **Updated CalendarController** — OAuth state now encodes origin for proper redirect back to wizard

### Files Created
- `app/Http/Controllers/Api/V1/OnboardingController.php`
- `database/migrations/..._add_onboarding_completed_at_to_users_table.php`
- `resources/js/stores/onboarding.js`
- `resources/js/views/onboarding/OnboardingView.vue`
- `resources/js/views/onboarding/steps/` — 7 step components (Welcome, Invite, Calendar, TaskList, Features, FeaturesExplainer, Complete)

### Files Modified
- `app/Models/User.php` — added `onboarding_completed_at` to fillable/casts
- `app/Http/Resources/UserResource.php` — exposes `onboarding_completed_at`
- `app/Http/Controllers/Api/V1/AuthController.php` — added `updateProfile` method
- `app/Http/Controllers/Api/V1/CalendarController.php` — origin param in OAuth state
- `app/Http/Controllers/Api/V1/FamilyController.php` — managed accounts auto-complete onboarding
- `resources/js/router/index.js` — onboarding route + guard
- `resources/js/App.vue` — hides sidebar/nav during wizard
- `resources/js/views/settings/SettingsView.vue` — "Re-run Setup Wizard" section
- `routes/api.php` — onboarding + user profile routes

---

## 2026-03-28 — Session 8: Laravel-Native MCP Server

### What Was Done
- **Replaced TypeScript MCP server with Laravel-native MCP** using `laravel/mcp` v0.6.4
  - Eliminated the separate Node.js process — MCP now runs directly in Laravel via `/mcp` endpoint
  - No HTTP round-trips: tools access Eloquent models and services directly
  - Auth via Sanctum bearer token (same token system, simpler setup)

- **Built 18 consolidated MCP tools** (down from 26 individual tools in the TypeScript server)
  - Each tool uses an `action` parameter to handle multiple operations, reducing schema/token overhead
  - All tools scoped to authenticated user's family via `ScopesToFamily` trait
  - Parent-only actions (deduct points, create rewards, manage vault) return errors for child users

- **Tool inventory:**
  - Family & Settings: `view-family`, `get-settings`, `search-family`
  - Tasks: `manage-task-lists`, `manage-tasks`, `complete-task`, `manage-tags`
  - Points & Rewards: `view-points`, `manage-points`, `manage-point-requests`, `manage-rewards`, `purchase-reward`
  - Badges & Events: `manage-badges`, `view-earned-badges`, `manage-featured-events`
  - Calendar & Vault: `view-calendar`, `manage-vault`, `manage-vault-access`

- **Full content coverage:** Points, rewards, badges, featured events, and settings now have MCP tools (previously 0% coverage)

### Files Created
- `routes/ai.php` — MCP route registration
- `app/Mcp/Servers/KinholdServer.php` — Main MCP server (18 tools)
- `app/Mcp/Tools/Concerns/ScopesToFamily.php` — Shared trait for user/family context
- `app/Mcp/Tools/ViewFamily.php`
- `app/Mcp/Tools/GetSettings.php`
- `app/Mcp/Tools/SearchFamily.php`
- `app/Mcp/Tools/ManageTaskLists.php`
- `app/Mcp/Tools/ManageTasks.php`
- `app/Mcp/Tools/CompleteTask.php`
- `app/Mcp/Tools/ManageTags.php`
- `app/Mcp/Tools/ViewPoints.php`
- `app/Mcp/Tools/ManagePoints.php`
- `app/Mcp/Tools/ManagePointRequests.php`
- `app/Mcp/Tools/ManageRewards.php`
- `app/Mcp/Tools/PurchaseReward.php`
- `app/Mcp/Tools/ManageBadges.php`
- `app/Mcp/Tools/ViewEarnedBadges.php`
- `app/Mcp/Tools/ManageFeaturedEvents.php`
- `app/Mcp/Tools/ViewCalendar.php`
- `app/Mcp/Tools/ManageVault.php`
- `app/Mcp/Tools/ManageVaultAccess.php`
- `.claude/commands/cleanup.md` — Post-merge cleanup command

### Files Modified
- `composer.json` — Added `laravel/mcp: ^0.6.4`

### Removed
- `mcp-server/` — Old TypeScript/Node.js MCP server (superseded by Laravel-native)

## 2026-03-17 — Session 7: Upsun Deployment & Google OAuth

### What Was Done
- **Deployed Kinhold to Upsun** at `family.kinhold.com`
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
- **Pushed to GitHub** — public repo at https://github.com/gregqualls/kinhold
  - `gh repo create kinhold --public --source . --push`

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
