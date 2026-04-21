## ─────────────────────────────────────────────
# Stage 1: PHP dependencies (Composer)
# ─────────────────────────────────────────────
FROM composer:2.7 AS composer

WORKDIR /app

# Copy full source first so Composer can scan all classes
COPY . .

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# ─────────────────────────────────────────────
# Stage 2: Node.js assets (Vite / Mix)
# ─────────────────────────────────────────────
FROM node:20-alpine AS node

WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm ci

COPY . .
COPY --from=composer /app/vendor ./vendor

RUN npm run build

# ─────────────────────────────────────────────
# Stage 3: Final production image
# ─────────────────────────────────────────────
FROM php:8.3-fpm-alpine

LABEL maintainer="you@example.com"

# ── System dependencies ──────────────────────
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    && docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache

# ── PHP configuration ────────────────────────
COPY docker/php/php.ini /usr/local/etc/php/conf.d/laravel.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# ── Nginx configuration ──────────────────────
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# ── Supervisor configuration ─────────────────
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf

# ── Application files ────────────────────────
WORKDIR /var/www/html

COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=composer /app/vendor ./vendor
COPY --chown=www-data:www-data --from=node /app/public/build ./public/build

# ── Bootstrap ────────────────────────────────
COPY docker/entrypoint.sh /start.sh
RUN chmod +x /start.sh \
    && mkdir -p storage/logs storage/framework/{sessions,views,cache} bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Render assigns PORT at runtime (default 10000)
ENV PORT=10000
EXPOSE ${PORT}

CMD ["/start.sh"]
