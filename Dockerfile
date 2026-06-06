FROM php:8.4-apache

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        curl \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libzip-dev \
        libicu-dev \
    ; \
    docker-php-ext-install \
        pdo_mysql \
        mysqli \
        bcmath \
        gd \
        mbstring \
        zip \
        intl \
        exif \
    ; \
    pecl install xdebug; \
    docker-php-ext-enable xdebug; \
    a2enmod rewrite autoindex; \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf; \
    { \
        echo '<Directory /var/www/html>'; \
        echo '    Options Indexes FollowSymLinks'; \
        echo '    AllowOverride All'; \
        echo '    Require all granted'; \
        echo '</Directory>'; \
    } > /etc/apache2/conf-available/browse.conf; \
    a2enconf browse; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*; \
    chown -R www-data:www-data /var/www/html

COPY config/php.ini /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html
EXPOSE 80

HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1
