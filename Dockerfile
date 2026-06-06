FROM php:8.4-apache

# Install PDO MySQL and MySQLi extensions
RUN docker-php-ext-install pdo_mysql mysqli

# Enable mod_rewrite (optional)
RUN a2enmod rewrite

# Ensure extensions are loaded (explicitly create ini files)
RUN echo "extension=pdo_mysql.so" > /usr/local/etc/php/conf.d/docker-php-ext-pdo_mysql.ini \
    && echo "extension=mysqli.so" > /usr/local/etc/php/conf.d/docker-php-ext-mysqli.ini

WORKDIR /var/www/html
EXPOSE 80
