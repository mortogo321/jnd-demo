#!/bin/sh
set -e

echo "🚀 Starting Laravel application..."

# Load APP_ENV from .env file to display in logs
APP_ENV=$(grep "^APP_ENV=" .env 2>/dev/null | cut -d'=' -f2 | tr -d '"' | tr -d "'" || echo "unknown")
DB_CONNECTION=$(grep "^DB_CONNECTION=" .env 2>/dev/null | cut -d'=' -f2 | tr -d '"' | tr -d "'" || echo "")

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --ansi
fi

# Run migrations for all environments (upsert - safe for both fresh and existing databases)
echo "📦 Running database migrations..."
php artisan migrate --force --ansi

# Run seeders ONLY in development and testing environments (NOT production)
if [ "$APP_ENV" = "local" ] || [ "$APP_ENV" = "testing" ]; then
    echo "🌱 Running database seeders..."
    php artisan db:seed --force --ansi
fi

# Publish Swagger assets and generate docs (NOT in production)
if [ "$APP_ENV" != "production" ]; then
    echo "📚 Setting up API documentation..."
    php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider" --force --ansi > /dev/null 2>&1 || true
    php artisan l5-swagger:publish-assets --ansi > /dev/null 2>&1 || true
    php artisan l5-swagger:generate --ansi > /dev/null 2>&1 || echo "⚠️  Swagger generation skipped (no annotations found)"
fi

# Clear and cache config for production
if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Optimizing for production..."
    php artisan config:cache --ansi
    php artisan route:cache --ansi
    php artisan view:cache --ansi
fi

echo "✅ Application ready!"
echo "   Environment: $APP_ENV"
echo "   Database: $DB_CONNECTION"
echo ""

# Clear all cached data before starting
echo "🧹 Clearing cached data..."
php artisan optimize:clear --ansi

# Execute the main command (php-fpm)
exec "$@"
