FROM php:8.2-fpm

# Install Nginx and envsubst (gettext-base)
RUN apt-get update && apt-get install -y nginx gettext-base \
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

# Write Nginx config template — uses $PORT injected by Railway at runtime
RUN cat > /etc/nginx/sites-available/default.template << 'NGINX'
server {
    listen ${PORT};
    root /var/www/html;
    index splash.php index.php index.html;

    # API routing — pass full URI to api/index.php
    location /api {
        try_files $uri /api/index.php?$query_string;
        location ~ \.php$ {
            fastcgi_pass 127.0.0.1:9000;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            fastcgi_param REQUEST_URI $request_uri;
            include fastcgi_params;
        }
    }

    # Main site routing
    location / {
        try_files $uri $uri/ /splash.php?$query_string;
    }

    # PHP-FPM handler
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param REQUEST_URI $request_uri;
        include fastcgi_params;
    }
}
NGINX

RUN mkdir -p api/uploads/products \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 api/uploads

# Startup: substitute $PORT into nginx config, then launch PHP-FPM + Nginx
CMD export PORT="${PORT:-80}" \
    && envsubst '${PORT}' < /etc/nginx/sites-available/default.template \
         > /etc/nginx/sites-available/default \
    && php-fpm -D \
    && nginx -g 'daemon off;'

EXPOSE 80
