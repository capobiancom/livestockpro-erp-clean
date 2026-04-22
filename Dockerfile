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

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    mysql-client \
    $PHPIZE_DEPS \
    && docker-php-ext-install -j$(nproc) bcmath gd zip pdo_mysql \
    && apk del $PHPIZE_DEPS

# Copy application files with proper ownership
COPY --chown=www-data:www-data . .

# Copy scripts and make them executable
RUN chmod +x scripts/*.sh

# Copy built assets from Node stage with proper ownership
COPY --from=assets-builder --chown=www-data:www-data /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Ensure storage and bootstrap/cache exist and have correct permissions
# Also ensure the entire app directory is owned by www-data for the install wizard
RUN mkdir -p storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    bootstrap/cache \
    && touch .env \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/.env

# Fix Nginx duplicate location errors by using a clean monolithic config
RUN rm -rf /etc/nginx/sites-enabled/* /etc/nginx/sites-available/* /etc/nginx/conf.d/* /nginx.conf
COPY custom-nginx.conf /etc/nginx/nginx.conf
COPY custom-nginx.conf /nginx.conf

EXPOSE 80

# The base image already has a startup script that handles Nginx + PHP-FPM
