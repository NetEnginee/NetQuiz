FROM php:8.4-fpm

# Install system dependencies including WebP, JPEG, Freetype
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libwebp-dev \
    libjpeg-dev \
    libfreetype6-dev

# Clean cache to reduce image size
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Configure GD with WebP, JPEG, and Freetype support
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp

# Install PHP extensions including opcache
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd opcache

# Copy OPcache configuration into conf.d directory
COPY php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Get the latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/webfeed/html

# Ensure upload and log directories exist with writable permissions for www-data
RUN mkdir -p /var/webfeed/html/public/uploads/materials \
             /var/webfeed/html/public/uploads/questions \
             /var/webfeed/html/logs \
    && chown -R www-data:www-data /var/webfeed/html/public/uploads /var/webfeed/html/logs \
    && chmod -R 0775 /var/webfeed/html/public/uploads /var/webfeed/html/logs
