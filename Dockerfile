FROM php:8.0-fpm

RUN apt-get update \
  && apt-get install -y libzip-dev
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN docker-php-ext-install zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
COPY laravel_install.sh /app/
ENTRYPOINT sh -x /app/laravel_install.sh
