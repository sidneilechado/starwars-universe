#!/bin/sh

set -e

# Set working directory
cd /var/www/html

# Ensure .env file exists and has the correct key
if [ ! -f .env ]; then
    echo "Copying environment configuration..."
    cp .env.docker .env
fi

# Generate app key if missing
if ! grep -q "APP_KEY=" .env || grep -q "APP_KEY=$" .env || grep -q "APP_KEY=\"\"" .env; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
until php -r "
try {
    \$pdo = new PDO('mysql:host=mysql;port=3306', 'starwars', 'starwars123');
    exit(0);
} catch (Exception \$e) {
    exit(1);
}
" 2>/dev/null; do
    echo "MySQL is unavailable - sleeping"
    sleep 2
done

echo "MySQL is ready!"

# Wait for redis to be ready
echo "Waiting for Redis to be ready..."
until php -r "
try {
    \$redis = new Redis();
    \$redis->connect('redis', 6379, 1);
    \$redis->ping();
    exit(0);
} catch (Exception \$e) {
    exit(1);
}
" 2>/dev/null; do
    echo "Redis is unavailable - sleeping"
    sleep 2
done

echo "Redis is ready!"

# Ensure proper permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Handle different container types
if [ "$1" = "app" ]; then
    echo "Starting main application container..."
    echo "Running migrations..."
    php artisan migrate --force
    echo "Starting supervisor..."
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
fi

# Clear caches for workers/scheduler
if [ "$1" = "queue:work" ] || [ "$1" = "schedule:work" ]; then
    echo "Clearing application caches..."
    php artisan config:clear || true
    php artisan cache:clear || true
fi

# Execute the main command
echo "Starting: $@"
exec "$@"
