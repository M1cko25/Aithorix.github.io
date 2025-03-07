# 1️⃣ Base PHP image
FROM php:8.2-fpm AS php

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    zip unzip git curl supervisor nginx \
    && docker-php-ext-install pdo pdo_pgsql gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy Laravel files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chmod -R 777 storage bootstrap/cache

# 2️⃣ Node.js stage for building frontend
FROM node:18 AS node

WORKDIR /app

COPY package.json package-lock.json vite.config.js /app/
COPY resources/js/ /app/resources/js/
COPY resources/css/ /app/resources/css/

RUN npm install && npm run build

# 3️⃣ Final Production Image with Nginx
FROM php:8.2-fpm

WORKDIR /var/www

# Copy files from previous PHP stage
COPY --from=php /var/www /var/www

# Copy built frontend from Node.js stage
COPY --from=node /app/public/build /var/www/public/build

# Install and configure Nginx
RUN apt-get update && apt-get install -y nginx

# Copy Nginx configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Expose HTTP port
EXPOSE 80

# Start Nginx & PHP-FPM
CMD service nginx start && php-fpm