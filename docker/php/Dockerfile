FROM php:8.4-fpm
RUN apt update -q;
RUN apt install -q -y libpq-dev;
RUN docker-php-ext-install pdo_pgsql pgsql;

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug;

