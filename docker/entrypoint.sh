#!/bin/sh

set -e

echo "Kinhold - Starting application..."

# Auto-generate APP_KEY if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    # Check if a key was previously generated and persisted
    if [ -f "/data/.app_key" ]; then
        export APP_KEY=$(cat /data/.app_key)
        echo "Loaded APP_KEY from /data/.app_key"
    else
        echo "No APP_KEY found, generating one..."
        php artisan key:generate --force --show > /tmp/app_key_output
        GENERATED_KEY=$(cat /tmp/app_key_output | tr -d '[:space:]')
        rm -f /tmp/app_key_output
        export APP_KEY="$GENERATED_KEY"

        # Persist to data volume if available
        if [ -d "/data" ]; then
            echo "$GENERATED_KEY" > /data/.app_key
            echo "APP_KEY persisted to /data/.app_key"
        fi
    fi
    # Update .env so artisan commands within this container work
    if grep -q '^APP_KEY=' .env 2>/dev/null; then
        sed -i "s|^APP_KEY=.*|APP_KEY=$APP_KEY|" .env
    else
        echo "APP_KEY=$APP_KEY" >> .env
    fi
fi

# Database initialization
if [ "$DB_CONNECTION" = "sqlite" ]; then
    echo "Using SQLite database..."

    # Create the SQLite file and parent directory if they don't exist
    DB_DIR=$(dirname "$DB_DATABASE")
    if [ ! -d "$DB_DIR" ]; then
        echo "Creating database directory: $DB_DIR"
        mkdir -p "$DB_DIR"
    fi
    if [ ! -f "$DB_DATABASE" ]; then
        echo "Creating SQLite database: $DB_DATABASE"
        touch "$DB_DATABASE"
    fi

    echo "SQLite is ready!"
else
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
fi

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

# Execute the main command (php-fpm, queue:work, artisan serve, etc.)
exec "$@"
