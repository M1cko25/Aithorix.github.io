# 1️⃣ Base image for PHP
FROM php:8.2-fpm AS php

# Install required system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    supervisor \
    && docker-php-ext-install pdo pdo_pgsql gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory inside the container
WORKDIR /var/www

# Copy Laravel project files
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions for Laravel storage/cache
RUN chmod -R 777 storage bootstrap/cache

# 2️⃣ Separate Node.js image for building frontend
FROM node:18 AS node

# Set working directory
WORKDIR /app

# Copy only frontend-related files
COPY package.json package-lock.json vite.config.js /app/
COPY resources/js/ /app/resources/js/

# Install frontend dependencies
RUN npm install && npm run dev

# 3️⃣ Final production image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Copy files from previous PHP stage
COPY --from=php /var/www /var/www

# Copy built frontend from Node.js stage
COPY --from=node /app/resources/js/dist /var/www/public/build

# Expose Laravel's PHP-FPM port
EXPOSE 9000

# Start Laravel FPM
CMD ["php-fpm"]
