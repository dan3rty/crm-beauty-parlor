FROM php:8.2-fpm-bullseye

# Suppress Node update notifications
ENV NO_UPDATE_NOTIFIER=true

RUN docker-php-ext-install pdo pdo_mysql
ADD setup/ /setup/
RUN /setup/app-docker-setup.sh

WORKDIR /var/www
