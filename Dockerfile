FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

# Install Apache, PHP and required extensions
RUN apt-get update && apt-get install -y \
    apache2 \
    php8.1 \
    php8.1-mysql \
    libapache2-mod-php8.1 \
    curl \
    unzip \
    && apt-get clean

# Enable mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Remove default Apache page
RUN rm -f /var/www/html/index.html

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

CMD ["apache2ctl", "-D", "FOREGROUND"]
