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

# Install system dependencies if needed (image already has most)
RUN apk add --no-cache libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev

# Copy application files
COPY . .

# Copy built assets from Node stage
COPY --from=assets-builder /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

# The base image already has a startup script that handles Nginx + PHP-FPM
