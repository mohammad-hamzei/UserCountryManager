# Use PHP 8.2 as the base image
FROM php:8.2-fpm

# Install system dependencies and required libraries
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite

# Install PHP extensions
RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app
