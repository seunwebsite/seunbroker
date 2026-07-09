FROM php:8.2-apache

# 1. Install dependencies
# We use 'libc-client-dev' and 'libkrb5-dev' 
# BUT we explicitly run apt-get update first.
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    zip \
    libssl-dev \
    libc-client-dev \
    libkrb5-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. Configure IMAP correctly
# The secret here is pointing the configure command to the right include path
RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl --with-imap=/usr/include/ \
    && docker-php-ext-install imap

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