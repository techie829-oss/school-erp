# Stage 1: Composer Build
FROM composer:latest as composer_build
WORKDIR /app
COPY src/composer.json src/composer.lock ./
# Install dependencies (no dev, optimize)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --ignore-platform-reqs

# Stage 2: Node Build
FROM node:20-alpine as node_build
WORKDIR /app
COPY src/package.json src/package-lock.json ./
RUN npm ci
COPY src ./
RUN npm run build

# Stage 3: Final Runtime Image
FROM php:8.2-fpm

# Install system dependencies (runtime only)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    default-mysql-client \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd opcache

# Copy custom PHP configuration
COPY docker/php/production.ini /usr/local/etc/php/conf.d/production.ini

# Set working directory
WORKDIR /var/www

# Copy Composer dependencies from Stage 1
COPY --from=composer_build /app/vendor /var/www/vendor

# Copy Node build assets from Stage 2
COPY --from=node_build /app/public/build /var/www/public/build

# Copy Application Code
COPY src /var/www

# Dump autoload to generate optimized classmap
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload --optimize

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Create system user
RUN useradd -G www-data,root -u 1000 -d /home/dev dev
RUN mkdir -p /home/dev/.composer && \
    chown -R dev:dev /home/dev

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
