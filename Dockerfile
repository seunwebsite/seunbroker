FROM php:8.2-apache

# 1. Install dependencies
# IMAP packages removed
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    zip \
    libssl-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. IMAP configuration block removed entirely

# 3. MySQLi
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable Apache rewrite
RUN a2enmod rewrite

# 4. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html/
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader

COPY . .
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80