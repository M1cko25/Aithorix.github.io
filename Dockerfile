# Base PHP image
FROM php:8.2-fpm AS php

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    supervisor \
    nginx \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_pgsql pgsql gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files first to leverage Docker cache
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy the rest of the application code
COPY . .

# Generate application key
RUN php artisan key:generate --force

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage bootstrap/cache

# Node.js stage for building frontend
FROM node:18 AS node

WORKDIR /app

# Copy only necessary files for npm
COPY package*.json vite.config.js ./
COPY resources/ ./resources/

# Install dependencies and build
RUN npm ci && npm run build

# Final Production Image
FROM php

# Copy built assets from Node stage
COPY --from=node /app/public/build /var/www/public/build

# Configure Nginx
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Create directory for Nginx pid file
RUN mkdir -p /run/nginx

# Expose port
EXPOSE 80

# Start script
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Set environment variables
ENV APP_ENV=production
ENV APP_DEBUG=false

CMD ["/usr/local/bin/start.sh"]