# Use the official PHP 8.3 Apache image
FROM php:8.3-apache

# Linux configuration
RUN apt-get update && \
    apt-get install -y libpq-dev zip libonig-dev libicu-dev && \
    pecl install xdebug redis && \
    docker-php-ext-install mbstring intl && \
    docker-php-ext-enable opcache xdebug mbstring redis intl && \
    a2enmod rewrite headers && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Copy composer and composer.json
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Add Apache congiguration
COPY vhost.conf /etc/apache2/sites-available/000-default.conf

# Increase memory limit
RUN echo 'memory_limit = 256M' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini

WORKDIR /var/www/html

# Add application files
COPY src src
COPY public public
COPY composer.json composer.json

RUN chown -R www-data:www-data composer.json

# We no longer need to do anything as root
USER www-data

RUN composer install --no-dev --no-scripts

# Use the default entrypoint for the base image
CMD ["apache2-foreground"]
