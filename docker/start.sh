#!/bin/bash

# Wait for database
echo "Waiting for database to be ready..."
sleep 10

# Run migrations
php artisan migrate --force

# Clear and cache config
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Nginx
service nginx start

# Start PHP-FPM
php-fpm 