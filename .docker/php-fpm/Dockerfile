FROM php:7.3-fpm-alpine

# Install Additional dependencies
RUN apk update && apk add --no-cache \
   build-base shadow nano curl gcc git bash \
   php7 \
   php7-fpm \
   php7-common \
   php7-pdo \
   php7-pdo_mysql \
   php7-mysqli \
   php7-mcrypt \
   php7-mbstring \
   php7-xml \
   php7-openssl \
   php7-json \
   php7-phar \
   php7-zip \
   php7-gd \
   php7-dom \
   php7-session \
   php7-zlib \
   php7-curl \
   npm \
   shadow \
   nginx \
   supervisor \
   curl

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-enable pdo_mysql

# Install PHP Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Remove Cache
RUN rm -rf /var/cache/apk/*

# Add UID '1000' to www-data
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# Copy existing application directory permissions
COPY ../../ /var/www/html

# Configure nginx
COPY ./config/nginx.conf /etc/nginx/nginx.conf

# Remove default server definition
RUN rm /etc/nginx/conf.d/default.conf

# Configure PHP-FPM
COPY ./config/fpm-pool.conf /etc/php7/php-fpm.d/www.conf

# Configure supervisord
COPY ./config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html

RUN git config --global --add safe.directory /var/www/html

RUN chown -R www-data:www-data ./

# Change current user to www
USER www-data

RUN npm install --legacy-peer-deps

RUN npm run production

RUN rm -rf node_modules

RUN composer install --no-interaction --optimize-autoloader

# Expose the port nginx is reachable on
EXPOSE 8080

# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# Configure a healthcheck to validate that everything is up&running
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/fpm-ping
