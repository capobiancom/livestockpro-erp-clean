# BuildKit cache mounts active (no syntax directive needed — Coolify uses BuildKit natively)
# ===========================================================
# Stage 1: Build Frontend Assets
# Cache: npm package downloads persist between deploys
# ===========================================================
FROM node:20-alpine AS assets-builder
WORKDIR /app

COPY package*.json ./

# --mount=type=cache keeps ~/.npm across builds on the same server
# npm ci will still verify/install but downloads are instant from cache
RUN --mount=type=cache,target=/root/.npm \
    npm ci --cache /root/.npm

COPY . .
RUN npm run build

# ===========================================================
# Stage 2: PHP Runtime
# Pre-built base with bcmath + mysql-client already installed.
# Rebuilt automatically via .github/workflows/build-base.yml
# ONLY when Dockerfile.base / composer.json / composer.lock change.
# ===========================================================
FROM ghcr.io/capobiancom/erp-php-base:latest

ENV SKIP_COMPOSER=1 \
    PHP_ERRORS_STDERR=1 \
    RUN_SCRIPTS=1 \
    REAL_IP_HEADER=1 \
    APP_ENV=production \
    WEBROOT=/var/www/html/public

WORKDIR /var/www/html

# bcmath + mysql-client + curl/wget already baked into the base image ✓

# Copy application source (vendor/ and node_modules/ excluded via .dockerignore)
COPY . .

RUN chmod +x scripts/*.sh

# Copy compiled frontend assets from Stage 1
COPY --from=assets-builder /app/public/build ./public/build

# Install PHP dependencies with composer cache mount
# --mount=type=cache keeps downloaded packages between builds
RUN --mount=type=cache,target=/root/.composer/cache \
    composer install --no-dev --optimize-autoloader --no-interaction

# Fix Nginx configuration + create storage symlink
RUN rm -rf /etc/nginx/sites-enabled/* /etc/nginx/sites-available/* \
        /etc/nginx/conf.d/* /nginx.conf \
    && mkdir -p /etc/nginx/sites-available /etc/nginx/sites-enabled \
    && rm -rf public/storage \
    && ln -s ../storage/app/public public/storage

COPY custom-nginx.conf /etc/nginx/nginx.conf
COPY custom-nginx.conf /nginx.conf

# Surgical permissions — ONLY on writable dirs, not the entire tree
RUN mkdir -p storage/framework/sessions \
        storage/framework/views \
        storage/framework/cache \
        storage/logs \
        bootstrap/cache \
    && touch .env \
    && chown -R www-data:www-data storage bootstrap/cache .env \
    && chmod -R 775 storage bootstrap/cache \
    && chmod 664 .env

EXPOSE 80
