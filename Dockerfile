# ===========================================================
# Single-stage build: sequential, no OOM from parallel stages
# Base image has: PHP 7.4 + nginx + bcmath + mysql-client
# ===========================================================
FROM ghcr.io/capobiancom/erp-php-base:latest

ENV SKIP_COMPOSER=1 \
    PHP_ERRORS_STDERR=1 \
    RUN_SCRIPTS=1 \
    REAL_IP_HEADER=1 \
    APP_ENV=production \
    WEBROOT=/var/www/html/public

WORKDIR /var/www/html

# Step 1: Install Node.js for frontend build (then remove it)
# Using a pinned alpine node from the official apk index
RUN apk add --no-cache nodejs npm

# Step 2: Copy source code
COPY . .

# Step 3: Build frontend assets (sequential, no OOM risk)
# Cache mount keeps ~/.npm between builds
RUN --mount=type=cache,target=/root/.npm \
    npm ci --cache /root/.npm \
    && npm run build \
    && npm cache clean --force

# Step 4: Remove Node.js — not needed at runtime
RUN apk del nodejs npm \
    && rm -rf /root/.npm /root/.config/npm node_modules

# Step 5: Create required directories BEFORE composer install
# artisan package:discover (post-autoload-dump) needs bootstrap/cache writable
RUN mkdir -p bootstrap/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/framework/cache \
        storage/logs

# Step 6: Install PHP dependencies
# Cache mount keeps downloaded packages between builds
RUN --mount=type=cache,target=/root/.composer/cache \
    composer install --no-dev --optimize-autoloader --no-interaction

# Step 7: Fix Nginx configuration + create storage symlink
RUN chmod +x scripts/*.sh \
    && rm -rf /etc/nginx/sites-enabled/* /etc/nginx/sites-available/* \
           /etc/nginx/conf.d/* /nginx.conf \
    && mkdir -p /etc/nginx/sites-available /etc/nginx/sites-enabled \
    && rm -rf public/storage \
    && ln -s ../storage/app/public public/storage

COPY custom-nginx.conf /etc/nginx/nginx.conf
COPY custom-nginx.conf /nginx.conf

# Step 8: Surgical permissions — ONLY writable dirs, not the whole tree
RUN touch .env \
    && chown -R www-data:www-data storage bootstrap/cache .env \
    && chmod -R 775 storage bootstrap/cache \
    && chmod 664 .env

EXPOSE 80
