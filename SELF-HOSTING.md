# Self-Hosting Kinhold

This guide covers everything you need to run Kinhold on your own server. Kinhold is designed to be self-hosted — you get 100% of features, no paywalls, no feature gating.

## Quick Start (Recommended)

The fastest way to try Kinhold. Uses SQLite in a single Docker container — no database server needed.

```bash
git clone https://github.com/gregqualls/kinhold.git
cd kinhold
./setup-simple.sh
```

Open [http://localhost:8000](http://localhost:8000) and register your family.

**Demo accounts** (if you want to explore first):

| Role | Email | Password |
|------|-------|----------|
| Parent | `parent@demo.local` | `password` |
| Child | `emma@demo.local` | `password` |

---

## Setup Options

### Option A: Simple (SQLite + Single Container)

Best for: trying it out, small families, low-traffic instances.

- **Database:** SQLite (file-based, zero config)
- **Cache:** File system
- **Queue:** Synchronous (inline)
- **Containers:** 1

```bash
./setup-simple.sh
```

Data is stored in a Docker volume (`kinhold-data`). It persists across container restarts and rebuilds.

### Option B: Production (PostgreSQL + Redis)

Best for: multiple families, higher traffic, background jobs, better performance.

- **Database:** PostgreSQL 16
- **Cache:** Redis 7
- **Queue:** Redis (async)
- **Containers:** 7 (app, nginx, postgres, redis, node, queue worker, scheduler)

```bash
cp .env.example .env
# Edit .env with your database credentials
chmod +x setup.sh && ./setup.sh
```

### Option C: Native (No Docker)

Best for: developers or hosts that already have PHP + PostgreSQL.

```bash
brew install php@8.3 composer postgresql@16 redis node  # macOS
# Or apt install php8.3 composer postgresql redis-server nodejs  # Ubuntu/Debian

git clone https://github.com/gregqualls/kinhold.git
cd kinhold
cp .env.example .env
# Edit .env with your database credentials
composer install
npm install && npm run build
php artisan key:generate
php artisan migrate
php artisan db:seed  # Optional: load demo data
php artisan serve
```

---

## Configuration

### Required

| Variable | Purpose | Default |
|----------|---------|---------|
| `APP_KEY` | Encryption key | Auto-generated on first boot (Docker) |
| `APP_URL` | Your domain | `http://localhost:8000` |
| `DB_CONNECTION` | Database driver | `pgsql` (use `sqlite` for simple setup) |
| `DB_DATABASE` | Database name or file path | `kinhold` or `/data/kinhold.sqlite` |

### Optional Services

Kinhold works without any of these. Features gracefully degrade — buttons and sections hide automatically when their backing service isn't configured.

#### Google OAuth (Sign in with Google)

Enables "Sign in with Google" on login/register pages.

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a project → Enable "Google+ API"
3. Create OAuth 2.0 credentials (Web application)
4. Add redirect URIs:
   - `http://localhost:8000/auth/google/callback` (dev)
   - `https://yourdomain.com/auth/google/callback` (production)

```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_AUTH_REDIRECT_URI=https://yourdomain.com/auth/google/callback
```

#### Google Calendar Sync

Enables syncing family members' Google Calendars into the family calendar view. Uses the same Google Cloud project as OAuth — just enable the Calendar API too.

1. In Google Cloud Console → Enable "Google Calendar API"
2. Add calendar redirect URI:
   - `http://localhost:8000/api/v1/calendar/callback` (dev)
   - `https://yourdomain.com/api/v1/calendar/callback` (production)

```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=https://yourdomain.com/api/v1/calendar/callback
```

Without Google Calendar, the calendar still works — you can create manual family events.

#### AI Chat (Claude)

Enables the AI chatbot that can answer questions about your family's tasks, calendar, and vault.

1. Get an API key from [console.anthropic.com](https://console.anthropic.com/settings/keys)

```env
ANTHROPIC_API_KEY=sk-ant-...
```

Alternatively, each family can bring their own key (BYOK) via Settings > API Configuration.

#### Email (Notifications)

Enables email verification, weekly digests, and task notifications.

```env
# SMTP (any provider)
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=kinhold@yourdomain.com
MAIL_FROM_NAME=Kinhold
```

Without email configured, the app works fine — email verification banners won't appear and notification emails won't send.

---

## Upgrading

### From Git

```bash
cd kinhold
git pull origin main
composer install --no-dev
npm install && npm run build
php artisan migrate
```

### Docker (Simple)

```bash
cd kinhold
git pull origin main
docker compose -f docker-compose.simple.yml build
docker compose -f docker-compose.simple.yml up -d
# Migrations run automatically on container start
```

### Docker (Production)

```bash
cd kinhold
git pull origin main
docker compose build
docker compose up -d
```

---

## Migrating from SQLite to PostgreSQL

When your family outgrows SQLite, you can migrate to PostgreSQL without losing data.

1. Export your SQLite data:
   ```bash
   docker compose -f docker-compose.simple.yml exec app php artisan db:export-json /data/export.json
   ```

2. Update your `.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=your-postgres-host
   DB_PORT=5432
   DB_DATABASE=kinhold
   DB_USERNAME=kinhold
   DB_PASSWORD=your-password
   ```

3. Run migrations on the new database:
   ```bash
   php artisan migrate
   ```

4. Import your data:
   ```bash
   php artisan db:import-json /data/export.json
   ```

> Note: The export/import commands are planned but not yet implemented. For now, use a tool like [pgloader](https://pgloader.io/) to migrate SQLite → PostgreSQL directly.

---

## Reverse Proxy

If you're running Kinhold behind nginx, Apache, or Caddy:

### Caddy (simplest)

```
yourdomain.com {
    reverse_proxy localhost:8000
}
```

### Nginx

```nginx
server {
    listen 80;
    server_name yourdomain.com;

    location / {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Set `APP_URL=https://yourdomain.com` and `VITE_API_URL=https://yourdomain.com/api/v1` in your `.env`.

---

## Backups

### SQLite

Your entire database is a single file. Back it up with:

```bash
# Copy the SQLite file from the Docker volume
docker compose -f docker-compose.simple.yml exec app cp /data/kinhold.sqlite /data/kinhold-backup-$(date +%Y%m%d).sqlite
```

Or mount the volume and copy directly:

```bash
docker cp kinhold-app-1:/data/kinhold.sqlite ./backups/kinhold-$(date +%Y%m%d).sqlite
```

### PostgreSQL

```bash
docker compose exec pgsql pg_dump -U kinhold kinhold > backup-$(date +%Y%m%d).sql
```

### Uploaded Files

Documents uploaded to the vault are stored in `storage/app/`. Back up this directory separately.

---

## Security Considerations

- **APP_KEY:** Never share or commit this. It encrypts vault data, sessions, and tokens. If you lose it, encrypted vault entries become unreadable.
- **Vault encryption:** Sensitive fields (SSNs, medical records, etc.) are encrypted at rest using Laravel's encryption. The encryption key is derived from APP_KEY.
- **HTTPS:** Always use HTTPS in production. Set `APP_ENV=production` and configure your reverse proxy with SSL.
- **Updates:** Pull updates regularly. Security fixes are applied to `main`.
- **Rate limiting:** Auth endpoints are rate-limited by default (5 attempts per minute).

---

## Troubleshooting

### App won't start

Check the logs:
```bash
# Docker simple
docker compose -f docker-compose.simple.yml logs app

# Docker production
docker compose logs app
```

### "No application encryption key"

The APP_KEY wasn't generated. Run:
```bash
php artisan key:generate
# Or for Docker:
docker compose -f docker-compose.simple.yml exec app php artisan key:generate
```

### Frontend shows blank page

The Vue assets may not be built:
```bash
npm run build
# Or rebuild the Docker image:
docker compose -f docker-compose.simple.yml build
```

### Google OAuth redirect error

Make sure your redirect URIs in Google Cloud Console exactly match your `.env`:
- Login: `https://yourdomain.com/auth/google/callback`
- Calendar: `https://yourdomain.com/api/v1/calendar/callback`

### Database migrations fail

```bash
php artisan migrate:status  # Check which migrations are pending
php artisan migrate --force  # Force run in production
```

---

## Architecture

```
                    ┌─────────────┐
                    │   Browser   │
                    └──────┬──────┘
                           │
                    ┌──────▼──────┐
                    │  Vue 3 SPA  │  ← Static assets served by PHP/Nginx
                    └──────┬──────┘
                           │ /api/v1/*
                    ┌──────▼──────┐
                    │  Laravel 11 │  ← REST API + MCP Server
                    └──┬───┬───┬──┘
                       │   │   │
              ┌────────┘   │   └────────┐
              ▼            ▼            ▼
        ┌──────────┐ ┌──────────┐ ┌──────────┐
        │ Database │ │  Redis   │ │ External │
        │ PG/SQLite│ │(optional)│ │ APIs     │
        └──────────┘ └──────────┘ └──────────┘
                                   Google Calendar
                                   Anthropic Claude
```

Simple setup uses SQLite + file cache (no Redis, no external APIs required).
Production setup uses PostgreSQL + Redis for better performance and async jobs.

---

## Getting Help

- [GitHub Issues](https://github.com/gregqualls/kinhold/issues) — Bug reports and feature requests
- [GitHub Discussions](https://github.com/gregqualls/kinhold/discussions) — Questions and community support
