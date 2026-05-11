# Multi-stage build for Kinhold

# Stage 1: Node - Build Vue frontend assets
FROM node:20-alpine AS node

RUN apk upgrade --no-cache

WORKDIR /app

COPY package*.json ./
COPY vite.config.js tailwind.config.js postcss.config.js ./

RUN npm ci
COPY . .
RUN npm run build

# Stage 2: PHP - Main application
FROM php:8.4-fpm-alpine AS php

# Patch all Alpine base packages to latest security fixes
RUN apk upgrade --no-cache

# Runtime libraries — stay in the final image
RUN apk add --no-cache \
    libpq \
    libpng \
    libjpeg-turbo \
    libzip \
    icu-libs \
    sqlite-libs \
    curl

# Build-time deps grouped in a virtual package so they can be removed cleanly
RUN apk add --no-cache --virtual .build-deps \
    postgresql-dev \
    sqlite-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    icu-dev \
    autoconf \
    gcc \
    g++ \
    make

# Compile PHP extensions, enable Redis, then remove all build deps in one layer
RUN docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pdo_sqlite \
        pgsql \
        gd \
        zip \
        bcmath \
        intl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps \
    && rm -rf /tmp/pear

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy application code
COPY . .

# Copy PHP and entrypoint configuration
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-app.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy built frontend assets from node stage
COPY --from=node /app/public/build ./public/build

# Dummy APP_KEY so php artisan package:discover can run during composer install.
# The real key is generated at runtime by the entrypoint and persisted to the data volume.
ENV APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=

# Create directories before composer install so bootstrap/cache exists when
# php artisan package:discover runs as a post-autoload-dump script.
RUN mkdir -p storage/app storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Install Composer dependencies
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Fix ownership after all files are in place
RUN chown -R www-data:www-data /app

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
