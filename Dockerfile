# Use the official PHP image
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    supervisor \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_pgsql gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory inside the container
WORKDIR /var/www

# Copy Laravel project files into the container
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions for Laravel storage/cache
RUN chmod -R 777 storage bootstrap/cache

# Install Node.js dependencies and build assets
RUN npm install && npm run build

# Expose port for Laravel
EXPOSE 9000

# Start Laravel FPM
CMD ["php-fpm"]
