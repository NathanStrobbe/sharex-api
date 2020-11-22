FROM php:7.4-apache

WORKDIR /var/www/html

COPY sharex-api.php /var/www/html/index.php
RUN mkdir /var/www/html/files && chown www-data:www-data /var/www/html/files

VOLUME /var/www/html/files
