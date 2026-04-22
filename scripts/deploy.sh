#!/bin/bash

echo "Running migrations and seeders..."

# Wait for DB to be ready
# (Already handled by Coolify healthchecks usually, but safe to have)

php artisan migrate --force

if [ "$RUN_SEEDERS" = "true" ]; then
    echo "Seeding database..."
    php artisan db:seed --force
fi

# Ensure storage link exists
php artisan storage:link --force

# Production optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deployment script finished."
