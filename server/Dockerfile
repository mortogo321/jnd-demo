# Multi-stage Dockerfile for Laravel API

# Build argument for environment file
ARG ENV_FILE=development

# Stage 1: Base PHP image with extensions
FROM php:8.4-fpm-alpine AS base

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    bash \
    curl \
    git \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# Install Redis extension
RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Stage 2: Development
FROM base AS development

# Copy application code
COPY server/ .

# Copy environment file as .env (after copying all files)
ARG ENV_FILE
RUN cp .env.${ENV_FILE} .env

# Set ENV_FILE as environment variable for runtime
ENV ENV_FILE=${ENV_FILE}

# Install dependencies with force for clean install
RUN composer install --no-interaction --prefer-dist --no-cache

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]

# Stage 3: Production
FROM base AS production

# Set production PHP configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copy application code
COPY server/ .

# Copy environment file as .env (after copying all files)
ARG ENV_FILE
RUN cp .env.${ENV_FILE} .env

# Set ENV_FILE as environment variable for runtime
ENV ENV_FILE=${ENV_FILE}

# Install all dependencies first (needed for pint)
RUN composer install --no-interaction --prefer-dist

# Run prebuild (format code, clear caches)
RUN composer run-script prebuild

# Install production dependencies (removes dev dependencies)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
