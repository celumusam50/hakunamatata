FROM php:8.2-apache

# Fix Apache MPM conflict: disable event, enable prefork (required for PHP)
RUN a2dismod mpm_event && a2enmod mpm_prefork

# Enable Apache mod_rewrite (needed for API routing)
RUN a2enmod rewrite

# Install PHP extensions needed for MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy all project files
COPY . .

# Install PHP dependencies
RUN cd api && composer install --no-dev --optimize-autoloader

# Set Apache to allow .htaccess overrides
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/allow-override.conf \
    && a2enconf allow-override

# Fix uploads folder permissions
RUN mkdir -p api/uploads/products \
    && chown -R www-data:www-data api/uploads \
    && chmod -R 755 api/uploads

EXPOSE 80
