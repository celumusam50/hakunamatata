FROM php:8.2-fpm

# Install Nginx + Supervisor
RUN apt-get update && apt-get install -y nginx supervisor \
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

# Nginx config
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

# Supervisor config — manages both PHP-FPM and Nginx
RUN printf '[supervisord]\n\
nodaemon=true\n\
logfile=/var/log/supervisor/supervisord.log\n\
pidfile=/var/run/supervisord.pid\n\
\n\
[program:php-fpm]\n\
command=php-fpm -F\n\
autostart=true\n\
autorestart=true\n\
stderr_logfile=/var/log/supervisor/php-fpm.err.log\n\
stdout_logfile=/var/log/supervisor/php-fpm.out.log\n\
\n\
[program:nginx]\n\
command=nginx -g "daemon off;"\n\
autostart=true\n\
autorestart=true\n\
stderr_logfile=/var/log/supervisor/nginx.err.log\n\
stdout_logfile=/var/log/supervisor/nginx.out.log\n' > /etc/supervisor/conf.d/supervisord.conf

RUN mkdir -p /var/log/supervisor

# Validate nginx config at build time
RUN nginx -t

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
