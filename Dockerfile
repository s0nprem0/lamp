FROM php:8.4-apache

ARG PHP_MEMORY_LIMIT=256M
ARG PHP_MAX_EXECUTION_TIME=30
ARG PHP_UPLOAD_MAX_FILESIZE=20M
ARG PHP_POST_MAX_SIZE=20M

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
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*

# PHP configuration
RUN echo "memory_limit = ${PHP_MEMORY_LIMIT}" > /usr/local/etc/php/conf.d/docker-php-custom.ini \
    && echo "max_execution_time = ${PHP_MAX_EXECUTION_TIME}" >> /usr/local/etc/php/conf.d/docker-php-custom.ini \
    && echo "upload_max_filesize = ${PHP_UPLOAD_MAX_FILESIZE}" >> /usr/local/etc/php/conf.d/docker-php-custom.ini \
    && echo "post_max_size = ${PHP_POST_MAX_SIZE}" >> /usr/local/etc/php/conf.d/docker-php-custom.ini

# Apache configuration
RUN a2enmod rewrite headers ssl; \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf; \
    { \
        echo '<Directory /var/www/html>'; \
        echo '    Options Indexes FollowSymLinks'; \
        echo '    AllowOverride All'; \
        echo '    Require all granted'; \
        echo '</Directory>'; \
    } > /etc/apache2/conf-available/browse.conf; \
    a2enconf browse; \
    sed -i 's/ServerTokens OS/ServerTokens Prod/' /etc/apache2/conf-available/security.conf; \
    sed -i 's/ServerSignature On/ServerSignature Off/' /etc/apache2/conf-available/security.conf

# Create non-root user
RUN useradd -r -u 1000 -g www-data webuser

WORKDIR /var/www/html
EXPOSE 80

HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1
