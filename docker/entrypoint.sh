#!/bin/bash

# Exit on error
set -e

# Wait for database to be ready (simple sleep for now, could use wait-for-it)
echo "Waiting for database connection..."
sleep 10

# Run migrations
echo "Running migrations..."
php artisan migrate --force || echo "Migration failed, but continuing..."

# Seed database if needed (optional)
# echo "Seeding database..."
# php artisan db:seed --force

# Clear caches
echo "Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Set permissions for storage again (just in case)
echo "Setting permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Execute the passed command
exec "$@"
