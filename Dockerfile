# Use the official PHP image with Apache web server
FROM php:8.2-apache

# 1. Update and install dependencies
# We add a temporary backports-style approach to ensure we get the right libraries
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    zip \
    libssl-dev \
    libc-client2007e-dev \
    libkrb5-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. Configure IMAP correctly
# The extension needs to know exactly where the header files are (which is /usr/include/imap)
RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install -j$(nproc) imap

# 3. Install MySQLi
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