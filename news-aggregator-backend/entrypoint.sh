#!/bin/sh

# Exit immediately if a command exits with a non-zero status
set -e

# Wait for MySQL service to be ready
echo "Waiting for MySQL..."
while ! nc -z db 3306; do
  sleep 1
done
echo "MySQL started"

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Clear and cache configurations
echo "Clearing and caching Laravel configurations..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start PHP-FPM in the foreground
echo "Starting PHP-FPM..."
php-fpm