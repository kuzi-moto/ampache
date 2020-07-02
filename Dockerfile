# Build Ampache
FROM composer AS builder
COPY . .
RUN composer install --prefer-source --no-interaction

# Build lightweight image
FROM php:7.4-apache-buster

# Additinal packages
RUN apt update && apt install -y \
    libjpeg62-turbo-dev \
    libpng-dev

# Additional extensions
RUN docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install pdo_mysql gd

RUN mkdir -m 777 /var/log/ampache

RUN apt install git -y

COPY --from=builder --chown=www-data:www-data "/app" "/var/www/html"
