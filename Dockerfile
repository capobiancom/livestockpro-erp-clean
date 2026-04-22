# --- Stage 1: Build Assets ---
FROM node:20-alpine AS assets-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# --- Stage 2: Application Runtime ---
FROM richarvey/nginx-php-fpm:3.1.6

# Set environment variables for richarvey image
ENV SKIP_COMPOSER=1
ENV PHP_ERRORS_STDERR=1
ENV RUN_SCRIPTS=1
ENV REAL_IP_HEADER=1
ENV APP_ENV=production
ENV WEBROOT=/var/www/html/public

WORKDIR /var/www/html

# Install bcmath using the standard phpize flow.
# Use --virtual to group build deps so they are removed in the same layer,
# keeping the image small. No need for libtool/autoconf/build-base separately.
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && docker-php-ext-install -j$(nproc) bcmath \
    && apk del .build-deps \
    && apk add --no-cache mysql-client curl wget \
    && rm -rf /var/cache/apk/* /tmp/*

# Copy application files
COPY . .

# Copy scripts and make them executable
RUN chmod +x scripts/*.sh

# Copy built assets from Node stage
COPY --from=assets-builder /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Fix Nginx configuration and ensure relative storage symlink
RUN rm -rf /etc/nginx/sites-enabled/* /etc/nginx/sites-available/* /etc/nginx/conf.d/* /nginx.conf \
    && mkdir -p /etc/nginx/sites-available /etc/nginx/sites-enabled \
    && rm -rf public/storage \
    && ln -s ../storage/app/public public/storage

COPY custom-nginx.conf /etc/nginx/nginx.conf
COPY custom-nginx.conf /nginx.conf

# Fix permissions SURGICALLY — only on writable dirs, NOT the entire tree
# This avoids the 8-minute `chown -R /var/www/html` that caused the build timeout
RUN mkdir -p storage/framework/sessions \
        storage/framework/views \
        storage/framework/cache \
        storage/logs \
        bootstrap/cache \
    && touch .env \
    && chown -R www-data:www-data \
        storage \
        bootstrap/cache \
        .env \
    && chmod -R 775 storage bootstrap/cache \
    && chmod 664 .env

EXPOSE 80

# The base image already has a startup script that handles Nginx + PHP-FPM
