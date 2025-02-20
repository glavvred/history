# Dockerfile
FROM php:8.3-fpm

# Устанавливаем системные зависимости
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libcurl4-openssl-dev \
    libxml2-dev \
    libssl-dev \
    zlib1g-dev \
    libzip-dev \
    libevent-dev \
    autoconf \
    build-essential \
    default-mysql-client \
    default-libmysqlclient-dev \
    && rm -rf /var/lib/apt/lists/*
    #libpq-dev \

# Устанавливаем расширения PHP
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip \
    mysqli
    #pdo_pgsql \
    #mbstring \
    #xml \
    #opcache \
    #intl

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# Копируем ТОЛЬКО файлы композера сначала
COPY composer.json ./

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-interaction --optimize-autoloader;

# Устанавливаем зависимости Composer с оптимизацией
#RUN if [ -f composer.lock ]; then \
#        composer install --no-interaction --optimize-autoloader; \
#    else \
#        composer update --no-interaction --optimize-autoloader; \
#    fi

# Копируем ВСЕ остальные файлы проекта
COPY . .

# Создаем скрипт для запуска миграций и сервера
RUN echo '#!/bin/sh' > /start.sh \
    && echo 'php bin/console doctrine:migrations:migrate --no-interaction' >> /start.sh \
    #&& echo 'php bin/console server:start 0.0.0.0:8000' >> /start.sh \
    && echo 'php-fpm' >> /start.sh \
    && chmod +x /start.sh

# Открываем порт для встроенного сервера Symfony
EXPOSE 8000

RUN chown -R 1000:1000 vendor
RUN chmod -R 775 vendor

# Команда запуска миграций и сервера
CMD ["/start.sh"]