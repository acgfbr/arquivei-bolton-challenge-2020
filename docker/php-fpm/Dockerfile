FROM php:7.3-fpm

MAINTAINER Antônio Guimarães <acgfbr@gmail.com>

RUN apt-get update && apt-get install -y &&  apt-get install -y git && apt-get install -y libzip-dev 
RUN apt-get install -y --no-install-recommends libbz2-dev zlib1g-dev && docker-php-ext-install zip bz2
RUN pecl install redis \
    && docker-php-ext-enable redis
RUN apt-get install -y libsqlite3-dev
RUN docker-php-ext-install pdo_sqlite \
&& docker-php-ext-enable pdo_sqlite
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

