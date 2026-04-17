#!/usr/bin/env bash
# Dev bootstrap + Laravel server.
# Idempotent — safe to re-run on every preview_start.
#
# 1. Creates SQLite DB if missing (when DB_CONNECTION=sqlite)
# 2. Runs pending migrations
# 3. Seeds demo data if the demo family is missing
# 4. Starts `php artisan serve`

set -e

cd "$(dirname "$0")/.."

DB_CONNECTION=$(php -r "require 'vendor/autoload.php'; \$app = require 'bootstrap/app.php'; echo config('database.default');" 2>/dev/null || echo "sqlite")

if [ "$DB_CONNECTION" = "sqlite" ]; then
  DB_PATH="database/database.sqlite"
  if [ ! -f "$DB_PATH" ]; then
    echo "[dev-laravel] Creating SQLite database at $DB_PATH"
    touch "$DB_PATH"
  fi
fi

echo "[dev-laravel] Running migrations..."
php artisan migrate --force

DEMO_EXISTS=$(php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo App\Models\Family::where('slug', 'q32-demo-family')->exists() ? 'yes' : 'no';
" 2>/dev/null | tr -d '[:space:]')

if [ "$DEMO_EXISTS" != "yes" ]; then
  echo "[dev-laravel] Demo family missing — seeding..."
  php artisan db:seed --force
else
  echo "[dev-laravel] Demo family present — skipping seed."
fi

echo "[dev-laravel] Starting php artisan serve on :8000"
exec php artisan serve
