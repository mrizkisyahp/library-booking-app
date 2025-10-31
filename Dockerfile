# Use Apache + PHP 8.3
FROM php:8.3-apache

# Install PHP extensions you need
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite (important for MVC routing)
RUN a2enmod rewrite

# Copy our Apache vhost config
COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Custom PHP configuration
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Set the working directory
WORKDIR /var/www/html
