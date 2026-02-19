FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    git curl libpng-dev oniguruma-dev libxml2-dev unzip nodejs npm postgresql-dev bash

RUN docker-php-ext-install pdo pdo_pgsql pgsql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader --no-interaction

COPY . .

RUN npm install && npm run build

RUN chmod -R 755 /var/www/html/bootstrap/cache /var/www/html/storage

EXPOSE 8080

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
