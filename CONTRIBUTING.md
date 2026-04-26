# Contributing to Kinhold

Thanks for your interest in contributing! Kinhold is an open-source family hub and we welcome contributions of all kinds.

## Before you start

1. **Read [PRINCIPLES.md](docs/PRINCIPLES.md).** Every contribution should align with our core product principles. The short version: keep it simple for busy parents, privacy-first, API-first, mobile-first.
2. **Check the [roadmap](docs/ROADMAP.md)** to see what's planned and where help is needed.
3. **Open an issue first** for anything beyond a small bug fix. This avoids duplicate work and lets us align on approach.

## Development setup

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- PostgreSQL 16
- Redis 7

### Quick start (macOS)

```bash
brew install php@8.3 composer postgresql@16 redis node
brew services start postgresql@16
brew services start redis
createdb kinhold

git clone https://github.com/gregqualls/kinhold.git
cd kinhold
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan db:seed

# Terminal 1:
npm run dev

# Terminal 2:
php artisan serve
```

Open http://localhost:8000. Demo accounts are created by the seeder.

### Docker alternative

```bash
chmod +x scripts/setup-dev.sh && ./scripts/setup-dev.sh
```

## Code conventions

- **Backend:** Laravel conventions, PSR-12 style. See [docs/CONVENTIONS.md](docs/CONVENTIONS.md).
- **Frontend:** Vue 3 Composition API with `<script setup>`. Tailwind CSS for styling. Pinia for state.
- **API routes:** All under `/api/v1/`. Every feature must be accessible through the API.
- **Database:** UUIDs for primary keys. Migrations are timestamped.

## Development pipeline

We use a structured pipeline to guarantee quality and safe output. If you're using Claude Code, these are available as slash commands:

```
/kickoff → code → /review → /check → /pr → /qa → /handoff → /merge → /cleanup
```

| Step | Command | Purpose |
|------|---------|---------|
| Start session | `/kickoff` | Read context, check state, offer to branch from next issue |
| Write code | — | Implement the feature or fix |
| Review changes | `/review` | Catch security, architecture, and convention issues |
| Quality checks | `/check` | Run all automated gates (Pint, Larastan, PHPUnit, ESLint, Vite) |
| Create PR | `/pr` | Push branch, link issues, run checks as gate |
| QA | `/qa` | Verify CI status + Upsun preview environment |
| Handoff | `/handoff` | Capture session context before merge |
| Merge | `/merge` | Squash merge, confirm production deploy |
| Cleanup | `/cleanup` | Prune branches and worktrees |

**Utility commands:** `/fix` (auto-fix lint/format), `/ship` (comprehensive audit), `/playbook` (interactive pipeline guide), `/issue-planner` (sprint planning)

## Submitting changes

1. Fork the repo and create a branch from `main`.
2. Make your changes. Keep commits focused.
3. Run quality checks: `./vendor/bin/pint --test && ./vendor/bin/phpstan analyse && ./vendor/bin/phpunit && npx vite build && npx eslint resources/js/`
4. Run the **ship checklist** below before opening your PR.
5. Open a pull request against `main` with a clear description of what and why.

## Ship checklist

A feature is **not done** until all applicable items are complete. This is enforced in our [feature issue template](.github/ISSUE_TEMPLATE/feature.yml).

- [ ] API endpoints implemented
- [ ] Vue frontend components built
- [ ] **MCP server tools added** — MCP is a primary interface, not an afterthought
- [ ] Pinia store created/updated
- [ ] **Database seeder updated** — self-hosters must see the feature with demo data on first run
- [ ] **Landing page updated** — if it's not on the landing page, it doesn't exist to new users
- [ ] README updated (feature list, screenshots)
- [ ] ROADMAP.md updated
- [ ] CHANGELOG.md updated
- [ ] Tests written

## Principles check

Before submitting, verify your changes against [PRINCIPLES.md](docs/PRINCIPLES.md):

- [ ] Can a busy parent figure this out in 30 seconds?
- [ ] Does it work for different family sizes and age ranges?
- [ ] Is it accessible (semantic HTML, ARIA labels, keyboard nav, contrast)?
- [ ] Is sensitive data handled securely?
- [ ] Do parents have appropriate control over this feature?
- [ ] Can an AI agent use this feature via the API/MCP?
- [ ] Does it work on mobile (375px)?
- [ ] Does it respect user privacy?

## Questions?

Open an issue or start a discussion. We're happy to help.
