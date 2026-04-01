#!/bin/bash

set -e

echo ""
echo "  _  ___       _           _     _ "
echo " | |/ (_)_ __ | |__   ___ | | __| |"
echo " | ' /| | '_ \| '_ \ / _ \| |/ _\` |"
echo " | . \| | | | | | | | (_) | | (_| |"
echo " |_|\_\_|_| |_|_| |_|\___/|_|\__,_|"
echo ""
echo " Simple Self-Hosted Setup"
echo " SQLite · No External Dependencies"
echo ""

# Check for Docker
if ! command -v docker &> /dev/null; then
    echo "Error: Docker is not installed."
    echo "Install Docker from https://docs.docker.com/get-docker/"
    exit 1
fi

# Check for Docker Compose (v2 plugin or standalone)
if docker compose version &> /dev/null; then
    COMPOSE="docker compose"
elif command -v docker-compose &> /dev/null; then
    COMPOSE="docker-compose"
else
    echo "Error: Docker Compose is not installed."
    echo "Install Docker Compose from https://docs.docker.com/compose/install/"
    exit 1
fi

COMPOSE_FILE="-f docker-compose.simple.yml"

# Step 1: Environment file
echo "1/5  Setting up environment..."
if [ ! -f .env ]; then
    cp .env.docker-simple .env
    echo "     Created .env from .env.docker-simple"
else
    echo "     .env already exists, keeping it"
fi

# Step 2: Build
echo "2/5  Building container (this may take a few minutes on first run)..."
$COMPOSE $COMPOSE_FILE build --quiet

# Step 3: Start
echo "3/5  Starting Kinhold..."
$COMPOSE $COMPOSE_FILE up -d

# Step 4: Wait for app to be ready
echo "4/5  Waiting for app to be ready..."
ATTEMPTS=0
MAX_ATTEMPTS=60
until curl -sf http://localhost:8000 > /dev/null 2>&1; do
    ATTEMPTS=$((ATTEMPTS + 1))
    if [ $ATTEMPTS -gt $MAX_ATTEMPTS ]; then
        echo "     App did not start in time. Check logs with:"
        echo "     $COMPOSE $COMPOSE_FILE logs app"
        exit 1
    fi
    sleep 2
done

# Step 5: Seed demo data
echo "5/5  Seeding demo data..."
$COMPOSE $COMPOSE_FILE exec app php artisan db:seed --force 2>/dev/null || true

echo ""
echo "  Kinhold is running!"
echo ""
echo "  Open:  http://localhost:8000"
echo ""
echo "  Demo accounts:"
echo "    Parent:  parent@demo.local / password"
echo "    Parent:  sarah@demo.local  / password"
echo "    Child:   emma@demo.local   / password"
echo ""
echo "  Manage:"
echo "    Stop:    $COMPOSE $COMPOSE_FILE down"
echo "    Restart: $COMPOSE $COMPOSE_FILE up -d"
echo "    Logs:    $COMPOSE $COMPOSE_FILE logs -f app"
echo ""
