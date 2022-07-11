FROM php:fpm-alpine

COPY wait-for-it.sh /usr/bin/wait-for-it

RUN chmod +x /usr/bin/wait-for-it

RUN apk --update --no-cache add git bash autoconf g++ make

RUN docker-php-ext-install pdo_mysql
RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN mkdir -p /var/www/current

WORKDIR /var/www/current

RUN git config --global --add safe.directory /var/www/current

CMD composer install ; wait-for-it database:3306 -- composer db ;  php-fpm

EXPOSE 9000
