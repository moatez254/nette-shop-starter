FROM php:8.2-fpm-alpine

RUN apk add --no-cache git unzip nginx supervisor bash \
 && docker-php-ext-install pdo pdo_sqlite

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . /var/www/html

RUN composer install --no-interaction --prefer-dist

# Nginx
COPY nginx.conf /etc/nginx/nginx.conf

CMD ["/bin/sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
