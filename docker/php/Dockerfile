FROM php:8.3-fpm-alpine

RUN apk add --no-cache libjpeg-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install pdo_mysql gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# php-fpm clears environment variables by default; keep them so getenv() sees .env values.
RUN printf '[www]\nclear_env = no\n' > /usr/local/etc/php-fpm.d/zz-env.conf
