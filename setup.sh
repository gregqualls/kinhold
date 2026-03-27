#!/bin/bash

# Kinhold - First-Time Setup Script
# Run this once after cloning the repo to get everything running.
set -e

echo ""
echo "╔═══════════════════════════════════════╗"
echo "║       Kinhold — First-Time Setup      ║"
echo "╚═══════════════════════════════════════╝"
echo ""

# Check Docker
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker Desktop first."
    echo "   https://www.docker.com/products/docker-desktop/"
    exit 1
fi

if ! docker info &> /dev/null; then
    echo "❌ Docker daemon is not running. Please start Docker Desktop."
    exit 1
fi

echo "✓ Docker is available"

# Check docker compose (v2 syntax)
if docker compose version &> /dev/null; then
    COMPOSE="docker compose"
elif command -v docker-compose &> /dev/null; then
    COMPOSE="docker-compose"
else
    echo "❌ Docker Compose is not available."
    exit 1
fi

echo "✓ Docker Compose is available ($COMPOSE)"
echo ""

# Step 1: Create .env from example
if [ ! -f .env ]; then
    echo "📋 Creating .env from .env.example..."
    cp .env.example .env
    echo "   ✓ .env created"
else
    echo "📋 .env already exists, skipping..."
fi
echo ""

# Step 2: Build Docker images
echo "🐳 Building Docker images (this may take a few minutes on first run)..."
$COMPOSE build --no-cache app
echo "   ✓ Images built"
echo ""

# Step 3: Install Composer dependencies
echo "📦 Installing PHP dependencies..."
$COMPOSE run --rm app composer install --prefer-dist
echo "   ✓ Composer dependencies installed"
echo ""

# Step 4: Generate Laravel keys
echo "🔑 Generating application key..."
$COMPOSE run --rm app php artisan key:generate --force
echo "   ✓ APP_KEY generated"
echo ""

# Step 5: Install Node dependencies and build frontend
echo "🎨 Installing frontend dependencies..."
$COMPOSE run --rm node npm ci 2>/dev/null || $COMPOSE run --rm app bash -c "cd /app && npm ci"
echo "   ✓ Node dependencies installed"

echo "🏗️  Building frontend..."
$COMPOSE run --rm node npm run build 2>/dev/null || $COMPOSE run --rm app bash -c "cd /app && npm run build"
echo "   ✓ Frontend built"
echo ""

# Step 6: Start all services
echo "🚀 Starting services..."
$COMPOSE up -d pgsql redis
echo "   Waiting for database to be healthy..."
sleep 5

# Wait for PostgreSQL to be ready
for i in {1..30}; do
    if $COMPOSE exec -T pgsql pg_isready -U kinhold -d kinhold &> /dev/null; then
        break
    fi
    sleep 1
done
echo "   ✓ Database is ready"
echo ""

# Step 7: Run migrations and seed
echo "📊 Running database migrations..."
$COMPOSE run --rm app php artisan migrate --force
echo "   ✓ Migrations complete"

echo "🌱 Seeding default data (vault categories, demo family)..."
$COMPOSE run --rm app php artisan db:seed --force
echo "   ✓ Database seeded"
echo ""

# Step 8: Start the full stack
echo "🚀 Starting all services..."
$COMPOSE up -d
echo ""

# Step 9: Create storage link
$COMPOSE exec -T app php artisan storage:link 2>/dev/null || true

echo ""
echo "╔═══════════════════════════════════════╗"
echo "║          Setup Complete! 🎉           ║"
echo "╚═══════════════════════════════════════╝"
echo ""
echo "  🌐 App:      http://localhost"
echo "  📡 API:      http://localhost/api/v1"
echo "  🐘 Database: localhost:5432"
echo "  📮 Redis:    localhost:6379"
echo ""
echo "  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  Next steps:"
echo "  1. Open http://localhost in your browser"
echo "  2. Register a new account (first user becomes family admin)"
echo "  3. Share the invite code with family members"
echo ""
echo "  To stop:      $COMPOSE down"
echo "  To view logs:  $COMPOSE logs -f"
echo "  To rebuild:    $COMPOSE up -d --build"
echo ""
