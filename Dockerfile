FROM composer:2 AS dependencies

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader

FROM php:8.3-apache

RUN docker-php-ext-install mysqli intl mbstring \
    && a2enmod rewrite

WORKDIR /var/www/html
COPY --from=dependencies /app/vendor ./vendor
COPY . .

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/*.conf \
        /etc/apache2/apache2.conf \
        /etc/apache2/conf-available/*.conf \
    && chown -R www-data:www-data writable

EXPOSE 80