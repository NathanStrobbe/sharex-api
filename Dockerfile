FROM php:7.4-apache

WORKDIR /var/www/html

COPY sharex-api.php /var/www/html/index.php
RUN mkdir /var/www/html/i && chown www-data:www-data /var/www/html/i

VOLUME /var/www/html/i

RUN sed -ri -e 's!upload_max_filesize = 2M!upload_max_filesize = 25M!g' /usr/local/etc/php/php.ini-*