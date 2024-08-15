FROM php:8.2-fpm-alpine

ARG GID
ARG UID
ARG PHP_INI_FILE

ENV GID=${GID:-1000}
ENV UID=${UID:-1000}
ENV PHP_INI_FILE=${PHP_INI_FILE:-laravel.ini}

RUN apk update && apk add --no-cache \
        libzip-dev \
        libxml2-dev \
        curl \
        wget \
        git \
        bash \
        zip \
        zlib-dev \
        libpng \
        libpng-dev \
        postgresql-dev \
        supervisor \
        busybox-suid \
        openssh-client \
        make \
        && docker-php-ext-install opcache pdo_pgsql pgsql gd zip

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Настройка Crond и Supervisor
RUN mkdir -p /etc/supervisor.d
COPY supervisord /etc/supervisor.d/supervisord.conf
COPY schedule /home/admin/schedule.sh
COPY $PHP_INI_FILE /usr/local/etc/php/conf.d/laravel.ini

# Создаем пользователя, от имени которого будут работать все процессы
RUN addgroup -g $GID admin && \
    adduser -D -u $UID -G admin -h /home/admin admin

# Указываем рабочую директорию для PHP
WORKDIR /var/www

# Устанавливаем права доступа для скрипта и папки
RUN chown admin:admin /home/admin/schedule.sh && \
    chmod 744 /home/admin/schedule.sh && \
    chown -R admin:admin /var/www

# Устанавливаем домашнюю директорию для пользователя
ENV HOME=/home/admin

# Переход на пользователя admin
USER admin

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor.d/supervisord.conf"]
