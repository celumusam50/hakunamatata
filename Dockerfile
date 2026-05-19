FROM php:8.2-fpm

# Install Nginx
RUN apt-get update && apt-get install -y nginx \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html
COPY . .

# Rename htaccess to .htaccess if needed
RUN if [ -f htaccess ] && [ ! -f .htaccess ]; then cp htaccess .htaccess; fi

# Install PHP dependencies
RUN cd api && composer install --no-dev --optimize-autoloader

# Configure Nginx with proper PHP and API routing
RUN echo 'server {\n\
    listen 80;\n\
    root /var/www/html;\n\
    index splash.php index.php index.html;\n\
\n\
    # Main site routing\n\
    location / {\n\
        try_files $uri $uri/ /index.php?$query_string;\n\
    }\n\
\n\
    # API routing - route to api/index.php\n\
    location /api/ {\n\
        try_files $uri $uri/ /api/index.php?$query_string;\n\
    }\n\
\n\
    # PHP-FPM handler\n\
    location ~ \.php$ {\n\
        fastcgi_pass 127.0.0.1:9000;\n\
        fastcgi_index index.php;\n\
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n\
        include fastcgi_params;\n\
    }\n\
}' > /etc/nginx/sites-available/default

RUN mkdir -p api/uploads/products \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 api/uploads

# Start PHP-FPM then Nginx in foreground
CMD php-fpm -D && nginx -g 'daemon off;'

EXPOSE 80
