FROM php:7.3-fpm-alpine3.14

# Update package list and install dependencies
RUN apk add --no-cache --update \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    freetype-dev \
    nodejs \
    npm

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

# Install required packages
RUN docker-php-ext-configure zip \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install pdo_mysql exif zip gd calendar sockets

WORKDIR /usr/share/nginx/html/

# Copy the codebase
COPY . ./

# Run composer install for production and give permissions
RUN sed 's_@php artisan package:discover_/bin/true_;' -i composer.json \
    && composer install --no-dev --optimize-autoloader \
    && composer clear-cache \
    && php artisan package:discover --ansi \
    && chmod -R 775 storage \
    && chown -R www-data:www-data storage \
    && mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache

# Copy entrypoint
COPY ./production/php-fpm/php-fpm-entrypoint /usr/local/bin/php-entrypoint

# Give permisisons to everything in bin/
RUN chmod a+x /usr/local/bin/*

ENTRYPOINT ["/usr/local/bin/php-entrypoint"]

CMD ["php-fpm"]
