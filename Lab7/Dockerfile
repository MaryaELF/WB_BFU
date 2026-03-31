FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libz-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_sqlite

RUN pecl install rdkafka && docker-php-ext-enable rdkafka

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN composer require nmred/kafka-php:^0.2.0

CMD ["php-fpm"]