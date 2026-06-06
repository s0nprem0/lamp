FROM php:8.4-apache

RUN docker-php-ext-install pdo_mysql mysqli \
    && a2enmod rewrite autoindex

# Enable directory listing for projects
RUN echo "<Directory /var/www/html/projects>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" > /etc/apache2/conf-available/projects.conf \
    && a2enconf projects

WORKDIR /var/www/html
EXPOSE 80
