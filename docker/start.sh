#!/bin/sh
set -e

echo "=================================================="
echo "  SKYNITY WiFi — Starting Container"
echo "=================================================="

# ── Storage link ─────────────────────────────────────
echo "[1/5] Storage link..."
php artisan storage:link --force 2>/dev/null || true

# ── Run migrations ───────────────────────────────────
echo "[2/5] Running migrations..."
php artisan migrate --force --no-interaction

# ── Cache config/routes/views ────────────────────────
echo "[3/5] Caching..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── Fix permissions ──────────────────────────────────
echo "[4/5] Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ── Start supervisor (nginx + php-fpm + queue) ───────
echo "[5/5] Starting services..."
echo "=================================================="
exec /usr/bin/supervisord -c /etc/supervisord.conf
