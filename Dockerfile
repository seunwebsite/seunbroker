# Use the official PHP image with Apache web server
FROM php:8.2-apache

# 1. Install system tools and IMAP dependencies
# We use --no-install-recommends to keep the image slim.
# libc-client-dev is the correct alias for the IMAP development headers in Bookworm.
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    zip \
    libssl-dev \
    libc-client-dev \
    libkrb5-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. Configure and install the PHP IMAP extension
# Using -j$(nproc) speeds up the compilation process significantly
RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install -j$(nproc) imap

# 3. Install MySQLi extension for database connections
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# 4. Get Composer from the official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /var/www/html/

# 5. Copy only composer files first (this makes future deploys much faster!)
COPY composer.json composer.lock* ./

# 6. RUN YOUR COMPOSER COMMAND
RUN composer install --no-dev --optimize-autoloader

# 7. Copy the rest of your PHP project files
COPY . .

# Update permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80