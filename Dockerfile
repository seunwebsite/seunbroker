# Use the official PHP image with Apache web server
FROM php:8.2-apache

# 1. Update and install
# We use 'libc-client-dev' but we also add a shell check to see if it exists
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    zip \
    libssl-dev \
    libkrb5-dev \
    libc-client-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. Configure IMAP specifically
RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install imap

# 3. MySQLi
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# 4. Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html/

# Copy composer files
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader

# Copy the rest of the project
COPY . .

# Update permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80