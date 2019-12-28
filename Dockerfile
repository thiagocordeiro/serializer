FROM php:7.2-fpm-alpine

WORKDIR /var/www/html
ENV COMPOSER_ALLOW_SUPERUSER 1
COPY ./ /var/www/html/

RUN apk update && \
    apk add --no-cache libzip-dev bash && \
    docker-php-ext-install zip && \
    docker-php-ext-configure zip && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer install --no-scripts && \
    curl https://raw.githubusercontent.com/thiagocordeiro/docker/master/.bashrc -o ~/.bashrc

ENTRYPOINT ["php-fpm"]

