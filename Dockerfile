FROM php:8.2-apache

# Enable required PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Create data directory with correct permissions
RUN mkdir -p /var/www/html/data/sessions \
    && chown -R www-data:www-data /var/www/html/data \
    && chmod -R 755 /var/www/html/data

# Apache configuration
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/cyberquest.conf \
    && a2enconf cyberquest

EXPOSE 80

CMD ["apache2-foreground"]
