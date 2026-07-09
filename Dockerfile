# Use the official PHP image with Apache web server
FROM php:8.2-apache

# 1. Install system tools (git, zip, etc.) AND IMAP dependencies (libc-client-dev, libkrb5-dev)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libc-client2007e-dev \
    libkrb5-dev \
    && rm -rf /var/lib/apt/lists/*

# Configure and install the PHP IMAP extension
RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install imap

# Install MySQLi extension for database connections
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# 2. Get Composer from the official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /var/www/html/

# 3. Copy only composer files first (this makes future deploys much faster!)
COPY composer.json composer.lock* ./

# 4. RUN YOUR COMPOSER COMMAND
RUN composer install --no-dev --optimize-autoloader

# 5. Copy the rest of your PHP project files
COPY . .

# Update permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80