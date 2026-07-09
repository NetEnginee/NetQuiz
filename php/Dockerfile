FROM php:8.4-fpm

# Install sistem dependensi yang dibutuhkan framework modern
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Hapus cache untuk mengurangi ukuran image
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP untuk MySQL (sangat krusial untuk koneksi database)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Ambil Composer versi terbaru untuk manajemen framework
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/webfeed/html