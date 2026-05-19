FROM php:8.2-apache

# Fix MPM conflict
RUN rm -f /etc/apache2/mods-enabled/mpm_event.conf \
          /etc/apache2/mods-enabled/mpm_event.load \
    && ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf \
    && ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
    && ln -sf /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load

# Install PHP extensions needed for MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

# Set working directory
WORKDIR /var/www/html

# Copy all project files
COPY . .

# Rename htaccess to .htaccess if needed
RUN if [ -f htaccess ] && [ ! -f .htaccess ]; then cp htaccess .htaccess; fi

# Install PHP dependencies
RUN cd api && composer install --no-dev --optimize-autoloader

# Configure Apache VirtualHost
RUN rm -f /var/www/html/index.html \
    && echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html\n\
    DirectoryIndex splash.php index.php\n\
    <Directory /var/www/html>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    <Directory /var/www/html/api>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Fix permissions
RUN mkdir -p api/uploads/products \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 api/uploads

EXPOSE 80
