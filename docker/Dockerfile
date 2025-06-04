FROM php:8.3.2-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        libicu-dev \
        libpq-dev \
        libxpm-dev \
        libvpx-dev \
        libzip-dev \
        cron \
        nano \
    && pecl install xdebug redis-6.0.2 \
    && docker-php-ext-enable xdebug redis \
    && docker-php-ext-install -j$(nproc) gd intl zip pgsql pdo_pgsql exif sockets \
    && docker-php-ext-configure gd \
        --with-freetype=/usr/include/ \
        --with-jpeg=/usr/include/ \
        --with-xpm=/usr/lib/x86_64-linux-gnu/

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Создаем пользователя
RUN groupadd -g 1000 www && \
    useradd -u 1000 -ms /bin/bash -g www www

# Переключаемся на пользователя www
USER www


