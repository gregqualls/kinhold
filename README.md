<p align="center">
  <img src="https://raw.githubusercontent.com/gregqualls/kinhold/main/public/images/logo-100.png" alt="Kinhold Logo" width="80" />
</p>

<h1 align="center">Kinhold</h1>

<p align="center">
  <strong>The open-source family hub. Tasks, calendar, vault, gamification, and AI — self-hosted, private, yours.</strong>
</p>

<p align="center">
  <a href="#features">Features</a> &bull;
  <a href="#quick-start">Quick Start</a> &bull;
  <a href="#tech-stack">Tech Stack</a> &bull;
  <a href="#mcp-server">MCP Server</a> &bull;
  <a href="#contributing">Contributing</a>
</p>

<p align="center">
  <a href="LICENSE"><img src="https://img.shields.io/badge/license-Elastic_2.0-blue.svg" alt="Elastic License 2.0" /></a>
  <a href="https://github.com/gregqualls/kinhold/stargazers"><img src="https://img.shields.io/github/stars/gregqualls/kinhold?style=social" alt="GitHub Stars" /></a>
</p>

---

Kinhold is a self-hosted family management app that brings tasks, calendar, sensitive documents, meal planning, and gamification into one interface. Built mobile-first with dark mode and multiple color themes on the Kin design system, it's designed for real families with kids who need a little motivation to get things done.

Live at [kinhold.app](https://kinhold.app).

**Try it now:** [app.kinhold.app/demo](https://app.kinhold.app/demo) — log in as the Johnson family and explore the full app, no signup required.

## Features

### Core Modules

- **Task Management** — Multiple task lists, priorities, due dates, assignees. Family tasks anyone can claim. Recurring tasks via RRULE (daily, weekly, monthly). Points awarded on completion.
- **Family Calendar** — Aggregate Google Calendars + ICS feeds from every family member, plus manual events anyone can create. Recurrence (weekly/monthly/yearly), visibility controls (visible/busy/private), and "feature on dashboard" with countdown banners. Tasks with due dates show on the calendar automatically. Month/week/day views, mobile-optimized.
- **Secure Vault** — Encrypted storage for sensitive family info (SSNs, medical records, insurance, financial data). WYSIWYG markdown editor, role-based permissions, tap-to-reveal sensitive fields, auto-clear clipboard, document uploads. AI-guided playbooks for common data entry.
- **Food** — Recipe library with photo upload (or AI import from a URL or photo), ingredients, steps, tags, and family ratings. Restaurant list with tags and ratings. Weekly meal planner that pulls from both recipes and restaurants.
- **Shopping** — Shopping lists with pre-shop checklists, per-item assignment, and one-tap "build shopping list from this week's meals." Integrated with the Food module so meal plans flow straight into the cart.
- **AI Assistant** — Natural language interface to all family data via MCP tools: "What tasks are due this week?", "Create a dentist appointment for Friday", "What's the wifi password?" Powered by Claude's tool_use API.
- **MCP Server** — 7 grouped tools for managing Kinhold through Claude Desktop or Claude Code. Full coverage of tasks, vault, calendar, points, badges, food, and family data. Laravel-native, no separate process.

### Gamification

Turn chores into a game your kids actually want to play.

- **Points** — Earn points by completing tasks. Parents can give kudos (+1 pt) or deduct points. Ledger-based architecture — every point is traceable, no balance mutations.
- **Leaderboard** — Family rankings over configurable periods (daily/weekly/monthly). Resets each period without touching point banks.
- **Rewards Store** — Parents create prizes with point costs (Sweets = 10 pts, Movie Pick = 40 pts). Kids purchase instantly.
- **Badges** — Steam-style achievements with hexagonal icons. 29 default badges auto-created for every family. Auto-triggered by milestones (first task completed, 7-day streak, 1000 points earned) or manually awarded by parents. Hidden badges show as "???" until earned.
- **Activity Feed** — Family-wide feed showing task completions, kudos, purchases, and badge unlocks.

### Quality of Life

- **Dark Mode** — Full dark mode across every view and component, with subtle gradient depth effects
- **Kin Design System** — Unified component library and design tokens across every authenticated view, with gradient accent surfaces and a built-in design-system page for reference
- **Mobile-First** — Designed for phones first, scales to desktop with a sidebar layout
- **Feature Toggles** — Parents can enable/disable any module (calendar, tasks, vault, chat, points, badges)
- **Parent/Child Roles** — Parents get full control; children see only what's shared with them
- **Managed Accounts** — Parents can create child accounts without email and switch into them
- **Google OAuth** — Sign in with Google, or use email/password
- **Easter Eggs** — Hidden surprises scattered throughout the app. Find them all to unlock secret badges.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 11, PHP 8.2+ |
| Frontend | Vue 3 (Composition API, `<script setup>`) |
| State | Pinia (8 stores) |
| Styling | Tailwind CSS |
| Database | PostgreSQL 16 (production) or SQLite (simple setup), UUIDs |
| Cache/Queue | Redis 7 |
| Auth | Laravel Sanctum + Google OAuth via Socialite |
| AI | Anthropic Claude API (multi-provider ready) |
| MCP Server | Laravel-native (PHP, 7 grouped tools) |
| Build | Vite 5 |
| Hosting | [Upsun](https://upsun.com) |

## Quick Start

### Easiest: Docker with SQLite (no dependencies)

```bash
git clone https://github.com/gregqualls/kinhold.git
cd kinhold
./setup-simple.sh
```

That's it. Open [http://localhost:8000](http://localhost:8000) and log in with `parent@demo.local` / `password`.

This runs Kinhold with SQLite in a single container — no PostgreSQL or Redis needed. Great for trying it out or running for a small family. To upgrade to the full production stack (PostgreSQL + Redis), see Option 2 below.

### Option 1: Native (macOS — Recommended)

```bash
# Install dependencies
brew install php@8.3 composer postgresql@16 redis node
brew services start postgresql@16
brew services start redis

# Clone and set up
git clone https://github.com/gregqualls/kinhold.git
cd kinhold
cp .env.example .env
composer install
npm install
php artisan key:generate

# Create database and seed demo data
createdb kinhold
php artisan migrate
php artisan db:seed

# Start dev servers (two terminals)
php artisan serve        # Terminal 1: API at localhost:8000
npm run dev              # Terminal 2: Vite at localhost:5173
```

### Option 2: Docker (PostgreSQL + Redis — production)

```bash
git clone https://github.com/gregqualls/kinhold.git
cd kinhold
cp .env.example .env
chmod +x scripts/setup-dev.sh && ./scripts/setup-dev.sh
```

### Demo Accounts

After seeding, log in with any of these:

| Role | Email | Password |
|------|-------|----------|
| Parent | `parent@demo.local` | `password` |
| Parent | `sarah@demo.local` | `password` |
| Child (16) | `emma@demo.local` | `password` |

Two additional children (Jake, 14 and Lily, 12) are managed accounts — switch to them from Settings.

## Configuration

### Environment Variables

Copy `.env.example` to `.env` and configure:

| Variable | Purpose |
|----------|---------|
| `DB_*` | PostgreSQL connection |
| `REDIS_*` | Redis connection |
| `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` | Google OAuth + Calendar |
| `ANTHROPIC_API_KEY` | AI chat (optional). Self-hosters bring their own Anthropic key; Kinhold supports BYOK out of the box. |

### Google Calendar + OAuth

1. Create a project in [Google Cloud Console](https://console.cloud.google.com/)
2. Enable the Google Calendar API
3. Create OAuth 2.0 credentials
4. Add redirect URIs: `http://localhost:8000/auth/google/callback` (dev) and your production URL
5. Copy client ID and secret to `.env`

## API

All routes prefixed with `/api/v1/`. Auth routes are public, everything else requires Sanctum authentication.

```
Auth:      POST /register, /login, /logout, GET /user
Tasks:     CRUD /tasks, /task-lists, POST /tasks/{id}/complete, /uncomplete
Vault:     CRUD /vault/entries, /vault/categories, permissions, documents
Calendar:  GET /calendar/events, /connections, POST /events, PUT /events/{id}, DELETE /events/{id}, POST /connect, /sync
Points:    GET /points/bank, /leaderboard, /feed, POST /kudos, /deduct
Rewards:   CRUD /rewards, POST /rewards/{id}/purchase
Badges:    CRUD /badges, POST /badges/{id}/award, DELETE /badges/{id}/revoke/{user}
Family:    GET /family, /members, POST /invite, PUT /settings
Chat:      POST /chat, GET /chat/history
Settings:  GET /settings, PUT /settings
```

## MCP Server

Kinhold includes a Laravel-native MCP server that lets you manage your family hub entirely through AI clients like Claude Desktop, Claude Code, Cursor, or Windsurf. The server runs at the `/mcp` endpoint using Laravel's built-in MCP support — no separate process, no TypeScript, no Node.js.

Authentication uses Sanctum bearer tokens. Generate a token in **Settings > MCP Token** within the app, then add it to your AI client config.

### Claude Desktop Configuration

```json
{
  "mcpServers": {
    "kinhold": {
      "type": "streamableHttp",
      "url": "https://app.kinhold.app/mcp",
      "headers": {
        "Authorization": "Bearer YOUR_SANCTUM_TOKEN"
      }
    }
  }
}
```

For local development, use `http://localhost:8000/mcp` as the URL.

### Available Tools (7)

Each tool is action-driven, accepting an `action` parameter (e.g. `list`, `create`, `update`, `delete`) plus payload, so a single tool covers an entire module.

| Tool | Covers |
|------|--------|
| kinhold-tasks | Tasks, completion (with points and badge side effects), and tags |
| kinhold-calendar | Family calendar: events (manual, external, and tasks), connections, featured and countdown events |
| kinhold-vault | Encrypted family vault with categories, entries, per-user access, and playbooks |
| kinhold-food | Recipes, restaurants, meal plans, meal presets, and shopping lists |
| kinhold-points | Points, kudos, point requests, rewards store, and auctions |
| kinhold-achievements | Badges and earned achievements |
| kinhold-family | Family info, settings, dashboard layout, and cross-module search |

All tools are scoped to the authenticated user's family. Parent-only operations (featured events, granting badges, family settings) are enforced server-side regardless of which client calls the tool.

## Project Structure

```
kinhold/
├── app/
│   ├── Console/Commands/       # Artisan commands (recurring tasks, badge seeding)
│   ├── Enums/                  # FamilyRole, TaskPriority, PointTransactionType, BadgeTriggerType
│   ├── Http/Controllers/Api/   # 16 REST controllers
│   ├── Mcp/                    # Laravel-native MCP server
│   │   ├── Servers/            # MCP server registration
│   │   └── Tools/              # 20 MCP tool classes
│   ├── Models/                 # 17 Eloquent models
│   ├── Policies/               # Authorization policies
│   └── Services/               # Business logic (Points, Badges, Calendar, Vault, Chat)
├── database/migrations/        # 30 migrations
├── resources/js/
│   ├── components/             # Vue components (layout, common, tasks, vault, points, badges, etc.)
│   ├── views/                  # 19 page views across 10 modules
│   ├── stores/                 # 8 Pinia stores
│   ├── composables/            # Vue composables (notifications, dark mode, themes, colors)
│   └── router/                 # Vue Router with auth guards
├── .upsun/                     # Production deployment config
└── docs/                       # Architecture, roadmap, conventions
```

## Deploying Your Own Instance

Kinhold is designed to be self-hosted. See **[SELF-HOSTING.md](SELF-HOSTING.md)** for the full guide, including:

- SQLite vs PostgreSQL setup options
- Optional service configuration (Google, AI, email)
- Reverse proxy examples (Caddy, Nginx)
- Backup strategies
- Upgrading and migration

**Quick version:**

1. Fork this repo on GitHub
2. Run `./setup-simple.sh` for a single-container SQLite setup, or use `docker compose up` for the full PostgreSQL + Redis stack
3. Set your environment variables (Google OAuth, Anthropic key are optional)
4. To pull upstream updates: `git remote add upstream https://github.com/gregqualls/kinhold.git && git pull upstream main`

## Contributing

Contributions are welcome! See [CONTRIBUTING.md](CONTRIBUTING.md) for setup instructions, code conventions, and our principles checklist. Every PR is evaluated against our [core product principles](PRINCIPLES.md).

Found a bug or have an idea? Open an issue on [GitHub Issues](https://github.com/gregqualls/kinhold/issues). Want to discuss the project? Start a thread in [GitHub Discussions](https://github.com/gregqualls/kinhold/discussions).

## Documentation

| Document | Purpose |
|----------|---------|
| [SELF-HOSTING.md](SELF-HOSTING.md) | Complete self-hosting guide |
| [PRINCIPLES.md](docs/PRINCIPLES.md) | Core product principles that guide every decision |
| [CONTRIBUTING.md](CONTRIBUTING.md) | How to contribute |
| [CLAUDE.md](CLAUDE.md) | Project context for AI assistants |
| [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) | Technical decisions and reasoning |
| [docs/ROADMAP.md](docs/ROADMAP.md) | Feature roadmap with status |
| [docs/CONVENTIONS.md](docs/CONVENTIONS.md) | Coding standards and patterns |
| [CHANGELOG.md](CHANGELOG.md) | Development log |

## Roadmap

See [docs/ROADMAP.md](docs/ROADMAP.md) for the full plan. Recently shipped and coming up:

**Recently shipped:**
- ~~Food, meal planning, restaurants, and shopping~~ — Recipes (with URL and photo import), restaurant list, weekly meal planner, integrated shopping lists
- ~~MCP-powered AI assistant~~ — Natural language interface across 7 grouped tools (tasks, calendar, vault, food, points, achievements, family)
- ~~PWA support~~ — Installable, offline-capable, with web push notifications
- ~~Kin design-system overhaul~~ — Every authenticated view, dashboard widget, and auth/onboarding surface unified onto Kin tokens and components
- ~~Public demo at `/demo`~~ — Deep-linkable Johnson-family walkthrough for marketing
- ~~Manual calendar mode~~ — Create events without Google, recurrence, visibility, featured on dashboard
- ~~Profile pictures and avatars~~ — Photo upload, 26 icon presets, color picker
- ~~Vault overhaul~~ — WYSIWYG markdown editor, playbooks, kids personal vault
- ~~GDPR data export and account deletion~~ ([#96](https://github.com/gregqualls/kinhold/issues/96))
- ~~Landing page split from SPA~~ ([#134](https://github.com/gregqualls/kinhold/issues/134))
- ~~Self-host single-family enforcement and AI usage limits~~ ([#137](https://github.com/gregqualls/kinhold/issues/137), [#138](https://github.com/gregqualls/kinhold/issues/138))

**Coming up:**
- Two-way Google Calendar sync ([#71](https://github.com/gregqualls/kinhold/issues/71))
- AI photo-to-calendar: snap a flyer, extract events ([#74](https://github.com/gregqualls/kinhold/issues/74))
- Family announcements and bulletin board ([#72](https://github.com/gregqualls/kinhold/issues/72))
- Allowance and money tracking for kids ([#73](https://github.com/gregqualls/kinhold/issues/73))
- Dashboard rearrange ([#26](https://github.com/gregqualls/kinhold/issues/26))

## License

Kinhold is licensed under the [Elastic License 2.0](LICENSE).

**Free to use:** self-host it, fork it, modify it, run it for your family or internally at your company.
**Not allowed:** white-labeling it, selling it, or offering it as a managed/hosted service to others.

---

<p align="center">
  Built with care by <a href="https://github.com/gregqualls">Greg Qualls</a> and <a href="https://claude.ai">Claude</a>.
</p>
