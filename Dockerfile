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
# Added libtool, autoconf, and build-base to ensure bcmath can be compiled
# Removed -j$(nproc) to avoid potential parallel build issues in restricted environments
RUN apk add --no-cache \
    mysql-client \
    curl \
    wget \
    libtool \
    autoconf \
    build-base \
    && docker-php-ext-install bcmath \
    && apk del build-base autoconf libtool

# Copy application files
COPY . .

# Copy scripts and make them executable
RUN chmod +x scripts/*.sh

# Copy built assets from Node stage
COPY --from=assets-builder /app/public/build ./public/build

# Ensure storage and bootstrap/cache exist and have correct permissions
# Also ensure the entire app directory is owned by www-data for the install wizard
RUN mkdir -p /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/framework/cache \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache \
    && touch /var/www/html/.env \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/.env

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Fix Nginx duplicate location errors by using a clean monolithic config
RUN rm -rf /etc/nginx/sites-enabled/* /etc/nginx/sites-available/* /etc/nginx/conf.d/* /nginx.conf \
    && mkdir -p /etc/nginx/sites-available /etc/nginx/sites-enabled \
    && touch /etc/nginx/sites-available/default.conf
COPY custom-nginx.conf /etc/nginx/nginx.conf
COPY custom-nginx.conf /nginx.conf

EXPOSE 80

# The base image already has a startup script that handles Nginx + PHP-FPM
