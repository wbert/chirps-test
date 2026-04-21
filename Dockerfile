# ─────────────────────────────────────────────
# Stage 1: PHP dependencies (Composer)
# ─────────────────────────────────────────────
FROM php:8.4-cli-alpine AS composer

# Install git + unzip + curl (needed by composer) and the Composer binary
RUN apk add --no-cache git unzip curl \
    && curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Copy full source first so Composer can scan all classes
COPY . .

# Extensions (gd/intl/zip/pdo_mysql/etc.) are installed in the final stage.
# Skip platform-extension checks here since we're only resolving/downloading.
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-req=ext-gd \
    --ignore-platform-req=ext-intl \
    --ignore-platform-req=ext-zip \
    --ignore-platform-req=ext-pdo_mysql \
    --ignore-platform-req=ext-bcmath \
    --ignore-platform-req=ext-exif \
    --ignore-platform-req=ext-pcntl

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
FROM php:8.4-fpm-alpine

LABEL maintainer="you@example.com"

# ── System dependencies ──────────────────────
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    gettext \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    postgresql-dev \
    && docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache \
    && docker-php-ext-enable pdo_pgsql pgsql

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
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh \
    && mkdir -p storage/logs storage/framework/{sessions,views,cache} bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Render assigns PORT at runtime (default 10000)
ENV PORT=10000
EXPOSE ${PORT}

CMD ["/entrypoint.sh"]
