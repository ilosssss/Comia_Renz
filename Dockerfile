FROM php:7.4-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project files into Apache server folder
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install || true

EXPOSE 80
CMD ["apache2-foreground"]
