FROM php:8.2-apache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    netcat-openbsd \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql zip

COPY ./App /var/www/html

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap

COPY ./App/.env.example /var/www/html/.env

RUN composer install

RUN php artisan key:generate

RUN a2enmod rewrite

EXPOSE 80
