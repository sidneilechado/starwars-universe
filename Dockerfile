FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    build-base \
    curl \
    git \
    zip \
    unzip \
    nodejs \
    npm \
    nginx \
    supervisor

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql pcntl

# Install Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Set permissions for Laravel directories
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Install dependencies (including dev for development environment)
RUN composer install --optimize-autoloader

# Install Node.js dependencies (need all dependencies for build)
RUN npm ci

# Build frontend assets
RUN npm run build

# Configure Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf
RUN mkdir -p /etc/nginx/conf.d
COPY docker/default.conf /etc/nginx/conf.d/default.conf

# Configure Supervisor
RUN mkdir -p /var/log/supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy and set permissions for entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy environment file for Docker
RUN cp .env.docker .env

# Generate application key
RUN php artisan key:generate --force

# Expose port
EXPOSE 8000

# Set entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Default command for main app container
CMD ["app"]
