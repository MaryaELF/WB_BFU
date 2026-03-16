FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libonig-dev \
    && docker-php-ext-install pdo_mysql mbstring \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY ./www /var/www/html
CMD ["php-fpm"]