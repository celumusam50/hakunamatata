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

RUN mkdir -p api/uploads/products \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 api/uploads

# Write startup script — builds nginx config at runtime using $PORT
RUN cat > /start.sh << 'EOF'
#!/bin/sh
PORT="${PORT:-80}"

cat > /etc/nginx/sites-available/default << NGINX
server {
    listen ${PORT};
    root /var/www/html;
    index splash.php index.php index.html;

    location /api {
        try_files \$uri /api/index.php?\$query_string;
        location ~ \.php$ {
            fastcgi_pass 127.0.0.1:9000;
            fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
            fastcgi_param REQUEST_URI \$request_uri;
            include fastcgi_params;
        }
    }

    location / {
        try_files \$uri \$uri/ /splash.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param REQUEST_URI \$request_uri;
        include fastcgi_params;
    }
}
NGINX

echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx on port ${PORT}..."
nginx -g 'daemon off;'
EOF

RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
