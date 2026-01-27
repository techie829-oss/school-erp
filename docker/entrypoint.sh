#!/bin/bash
set -e

# Set permissions for storage (CRITICAL for Laravel execution)
# We do this here to ensure it works even if volumes are mounted
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Execute the main command (php-fpm)
exec "$@"
