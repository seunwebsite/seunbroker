# Use the official PHP image with Apache
FROM php:8.2-apache

# 1. Update and install system dependencies
# We use 'libc-client-dev' which is the correct header package for Debian Bookworm
# Note: Debian Bookworm requires 'libimap-dev' or 'libc-client-dev'. 
# If 'libc-client-dev' fails, we update the package index first.
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    zip \
    libssl-dev \
    libc-client-dev \
    libkrb5-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. Configure and install IMAP
# The --with-imap-ssl and --with-kerberos flags are required for Gmail
RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install imap

# 3. Install MySQLi
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# 4. Enable Apache mod_rewrite
RUN a2enmod rewrite

# 5. Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html/

# 6. Install PHP dependencies
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader

# 7. Copy project files
COPY . .

# Update permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80