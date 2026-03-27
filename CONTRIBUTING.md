# Contributing to Kinhold

Thanks for your interest in contributing! Kinhold is an open-source family hub and we welcome contributions of all kinds.

## Before you start

1. **Read [PRINCIPLES.md](PRINCIPLES.md).** Every contribution should align with our core product principles. The short version: keep it simple for busy parents, privacy-first, API-first, mobile-first.
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
chmod +x setup.sh && ./setup.sh
```

## Code conventions

- **Backend:** Laravel conventions, PSR-12 style. See [docs/CONVENTIONS.md](docs/CONVENTIONS.md).
- **Frontend:** Vue 3 Composition API with `<script setup>`. Tailwind CSS for styling. Pinia for state.
- **API routes:** All under `/api/v1/`. Every feature must be accessible through the API.
- **Database:** UUIDs for primary keys. Migrations are timestamped.

## Submitting changes

1. Fork the repo and create a branch from `main`.
2. Make your changes. Keep commits focused.
3. Ensure the frontend builds cleanly: `npx vite build`
4. Open a pull request against `main` with a clear description of what and why.

## Principles check

Before submitting, verify your changes against [PRINCIPLES.md](PRINCIPLES.md):

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
