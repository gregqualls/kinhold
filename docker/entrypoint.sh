#!/bin/sh

set -e

echo "Q32 Hub - Starting application..."

# Wait for PostgreSQL to be ready
echo "Waiting for PostgreSQL..."
ATTEMPTS=0
MAX_ATTEMPTS=30
until PGPASSWORD=$DB_PASSWORD psql -h "$DB_HOST" -U "$DB_USERNAME" -d "$DB_DATABASE" -c "SELECT 1" 2>/dev/null; do
    ATTEMPTS=$((ATTEMPTS + 1))
    if [ $ATTEMPTS -gt $MAX_ATTEMPTS ]; then
        echo "PostgreSQL failed to start in time"
        exit 1
    fi
    echo "PostgreSQL not ready yet... ($ATTEMPTS/$MAX_ATTEMPTS)"
    sleep 1
done

echo "PostgreSQL is ready!"

# Run migrations (safe to run even if already up to date)
echo "Running migrations..."
php artisan migrate --force

# Cache configuration in production
if [ "$APP_ENV" = "production" ]; then
    echo "Caching configuration..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "Application initialization complete!"

# Execute the main command (php-fpm, queue:work, etc.)
exec "$@"
