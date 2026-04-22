#!/bin/bash

echo "Running migrations and seeders..."

# Wait for DB to be ready
# (Already handled by Coolify healthchecks usually, but safe to have)

php artisan migrate --force
php artisan db:seed --force
php artisan storage:link

echo "Deployment script finished."
