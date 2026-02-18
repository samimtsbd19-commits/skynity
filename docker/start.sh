#!/bin/sh
set -e

echo "=================================================="
echo "  SKYNITY WiFi — Starting Container"
echo "=================================================="

# ── Generate APP_KEY if not set ──────────────────────
if [ -z "$APP_KEY" ]; then
    echo "[0/6] APP_KEY is empty — generating..."
    php artisan key:generate --force
else
    echo "[0/6] APP_KEY is set."
fi

# ── Wait for MySQL ────────────────────────────────────
echo "[1/6] Waiting for database..."
MAX_TRIES=30
COUNT=0
until php -r "
try {
    new PDO(
        'mysql:host=' . (getenv('DB_HOST') ?: '127.0.0.1') . ';port=' . (getenv('DB_PORT') ?: '3306'),
        getenv('DB_USERNAME') ?: 'root',
        getenv('DB_PASSWORD') ?: ''
    );
    exit(0);
} catch (Exception \$e) { exit(1); }
" > /dev/null 2>&1; do
    COUNT=$((COUNT + 1))
    if [ "$COUNT" -ge "$MAX_TRIES" ]; then
        echo "ERROR: Database not ready after $MAX_TRIES tries. Check DB credentials!"
        exit 1
    fi
    echo "  Database not ready, retrying ($COUNT/$MAX_TRIES)..."
    sleep 2
done
echo "  Database is ready!"

# ── Storage link ─────────────────────────────────────
echo "[2/6] Storage link..."
php artisan storage:link --force 2>/dev/null || true

# ── Run migrations ───────────────────────────────────
echo "[3/6] Running migrations..."
php artisan migrate --force --no-interaction

# ── Cache config/routes/views ────────────────────────
echo "[4/6] Caching..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── Fix permissions ──────────────────────────────────
echo "[5/6] Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ── Start supervisor (nginx + php-fpm + queue) ───────
echo "[6/6] Starting services..."
echo "=================================================="
exec /usr/bin/supervisord -c /etc/supervisord.conf
