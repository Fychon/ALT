FROM php:7.4-apache
RUN docker-php-ext-install mysqli calendar
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY ./public /var/www/html/

