FROM php:8.2-apache

# Use the project root as Apache document root.
WORKDIR /var/www/html

# Allow .htaccess overrides and disable directory listing.
RUN a2enmod rewrite && \
    sed -ri "s#AllowOverride None#AllowOverride All#g" /etc/apache2/apache2.conf && \
    sed -ri "s#Options Indexes FollowSymLinks#Options FollowSymLinks#g" /etc/apache2/apache2.conf
