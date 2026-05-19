FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install Apache, PHP and required extensions
RUN apt-get update && apt-get install -y \
    apache2 \
    php8.1 \
    php8.1-mysql \
    php8.1-xml \
    php8.1-mbstring \
    php8.1-curl \
    libapache2-mod-php8.1 \
    curl \
    unzip \
    && apt-get clean

# Make php8.1 the default php
RUN ln -sf /usr/bin/php8.1 /usr/bin/php

# Enable mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy all project files
COPY . .

# Install PHP dependencies
RUN cd api && composer install --no-dev --optimize-autoloader

# Rename htaccess files (saved without dot)
RUN if [ -f /var/www/html/htaccess ] && [ ! -f /var/www/html/.htaccess ]; then \
        cp /var/www/html/htaccess /var/www/html/.htaccess; \
    fi

# Remove Ubuntu default Apache page and configure VirtualHost with api directory
RUN rm -f /var/www/html/index.html /var/www/html/index.htm \
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

# Fix uploads folder permissions
RUN mkdir -p api/uploads/products \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 api/uploads

EXPOSE 80

CMD ["apache2ctl", "-D", "FOREGROUND"]
