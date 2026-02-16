#!/bin/bash
set -e

echo "Starting School ERP container..."

# Fix permissions (critical for Laravel)
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Wait for database
echo "Waiting for database connection..."
until php artisan db:show 2>/dev/null; do
    echo "Database not ready, waiting..."
    sleep 2
done
echo "✓ Database connected"

# Run migrations if flag is set
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "Running migrations..."
    php artisan migrate --force
fi

# Clear and cache
php artisan config:clear
php artisan cache:clear

echo "✓ Container ready"

# Execute main command (php-fpm)
exec "$@"