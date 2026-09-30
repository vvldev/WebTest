FROM php:8.3-fpm-alpine

RUN docker-php-ext-install pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# php-fpm clears environment variables by default; keep them so getenv() sees .env values.
RUN printf '[www]\nclear_env = no\n' > /usr/local/etc/php-fpm.d/zz-env.conf
