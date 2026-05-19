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

# Write nginx config — listen on 80 (Railway maps this automatically)
RUN printf 'server {\n\
    listen 80;\n\
    root /var/www/html;\n\
    index splash.php index.php index.html;\n\
\n\
    location /api {\n\
        try_files $uri /api/index.php?$query_string;\n\
        location ~ \\.php$ {\n\
            fastcgi_pass 127.0.0.1:9000;\n\
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n\
            fastcgi_param REQUEST_URI $request_uri;\n\
            include fastcgi_params;\n\
        }\n\
    }\n\
\n\
    location / {\n\
        try_files $uri $uri/ /splash.php?$query_string;\n\
    }\n\
\n\
    location ~ \\.php$ {\n\
        fastcgi_pass 127.0.0.1:9000;\n\
        fastcgi_index index.php;\n\
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n\
        fastcgi_param REQUEST_URI $request_uri;\n\
        include fastcgi_params;\n\
    }\n\
}\n' > /etc/nginx/sites-available/default

# Verify nginx config is valid at build time
RUN nginx -t

CMD php-fpm -D && nginx -g 'daemon off;'

EXPOSE 80
