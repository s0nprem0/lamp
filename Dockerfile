FROM php:8.4-apache

# Install extensions once during the image build stage
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable apache rewrite module if needed for frameworks like Laravel/Wordpress
RUN a2enmod rewrite
