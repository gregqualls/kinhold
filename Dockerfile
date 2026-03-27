# Multi-stage build for Kinhold

# Stage 1: Node - Build Vue frontend assets
FROM node:20-alpine as node

WORKDIR /app

# Copy package files
COPY package*.json ./
COPY vite.config.js tailwind.config.js postcss.config.js ./

# Install dependencies and build
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: PHP - Main application
FROM php:8.3-fpm-alpine as php

# Install system dependencies (autoconf needed for pecl)
RUN apk add --no-cache \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    icu-dev \
    supervisor \
    curl \
    git \
    zip \
    autoconf \
    gcc \
    g++ \
    make

# Install PHP extensions
RUN docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    gd \
    zip \
    bcmath \
    intl

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Clean up build dependencies
RUN apk del autoconf gcc g++ make

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application code
COPY . .

# Copy PHP configuration
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-app.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy built frontend assets from node stage
COPY --from=node /app/public/build ./public/build

# Install composer dependencies
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Create necessary directories
RUN mkdir -p storage/app storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data /app

# Expose PHP-FPM port
EXPOSE 9000

# Set entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Default command
CMD ["php-fpm"]
