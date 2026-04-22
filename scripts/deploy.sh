#!/bin/bash

echo "Running migrations and seeders..."

# Wait for DB to be ready
# (Already handled by Coolify healthchecks usually, but safe to have)

php artisan migrate --force
php artisan db:seed --force

echo "Deployment script finished."
