# ─────────────────────────────────────────────────────────────
#  SKYNITY WiFi — Production Dockerfile
#  PHP 8.3 + Nginx + Supervisor (Alpine)
#  Coolify / Docker deployment
# ─────────────────────────────────────────────────────────────
FROM php:8.3-fpm-alpine AS base

LABEL maintainer="SKYNITY WiFi"
LABEL description="MikroTik Hotspot Management System"

# ── System dependencies ──────────────────────────────────────
RUN apk add --no-cache \
    nginx \
    supervisor \
    git \
    curl \
    unzip \
    bash \
    # PHP extension dependencies
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    # Networking (for MikroTik API socket)
    openssl

# ── PHP extensions ───────────────────────────────────────────
RUN docker-php-ext-configure gd \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        bcmath \
        gd \
        zip \
        mbstring \
        opcache \
        pcntl \
        sockets

# ── Composer ─────────────────────────────────────────────────
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# ── PHP config ───────────────────────────────────────────────
COPY docker/php.ini /usr/local/etc/php/conf.d/skynity.ini

# ── App source ───────────────────────────────────────────────
WORKDIR /var/www/html
COPY . .

# ── Install PHP dependencies (production, no dev) ────────────
RUN composer install \
        --no-dev \
        --optimize-autoloader \
        --no-interaction \
        --no-progress \
    && composer clear-cache

# ── Permissions ──────────────────────────────────────────────
RUN chown -R www-data:www-data \
        storage \
        bootstrap/cache \
    && chmod -R 775 \
        storage \
        bootstrap/cache

# ── Copy server configs ──────────────────────────────────────
COPY docker/nginx.conf      /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/start.sh         /start.sh
RUN  chmod +x /start.sh

# ── Healthcheck ──────────────────────────────────────────────
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost/up || exit 1

EXPOSE 80

CMD ["/start.sh"]
