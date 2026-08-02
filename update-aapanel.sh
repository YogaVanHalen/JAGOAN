#!/bin/bash

# ==============================================================================
# 🚀 JAGOAN - Automated Update Script for aaPanel / VPS Linux
# ==============================================================================

echo "======================================================="
echo "   MEMULAI PROSES UPDATE APLIKASI JAGOAN               "
echo "======================================================="

# Dapatkan lokasi script saat ini
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR" || exit 1

PROJECT_DIR="$SCRIPT_DIR"
echo "📍 Lokasi Projek: $PROJECT_DIR"

# Bypass Composer superuser warning & Git dubious ownership warning
export COMPOSER_ALLOW_SUPERUSER=1
git config --global --add safe.directory "$PROJECT_DIR" 2>/dev/null
git config --global --add safe.directory "*" 2>/dev/null

# Hapus pembatasan .user.ini aaPanel jika ada
chattr -i .user.ini public/.user.ini 2>/dev/null
rm -f .user.ini public/.user.ini

# 1. Pull pembaruan kode terbaru dari GitHub (Force Reset to origin/main)
echo "📥 Menarik pembaruan kode terbaru dari GitHub (git fetch & reset)..."
git fetch origin main
git reset --hard origin/main

# Auto inject Cloudflare Turnstile keys placeholder into .env if missing
if [ -f .env ]; then
    if ! grep -q "TURNSTILE_SITE_KEY" .env; then
        echo "" >> .env
        echo "# Cloudflare Turnstile Keys" >> .env
        echo "TURNSTILE_SITE_KEY=" >> .env
        echo "TURNSTILE_SECRET_KEY=" >> .env
    fi
fi

# 2. Update dependensi Composer jika ada perubahan paket PHP
echo "📦 Mengompres & meng-update dependensi Composer..."
if command -v composer &> /dev/null; then
    composer install --no-interaction --prefer-dist --optimize-autoloader
elif [ -f composer.phar ]; then
    php composer.phar install --no-interaction --prefer-dist --optimize-autoloader
fi

# 3. Jalankan Migrasi Database Baru (Tanpa Menghapus Data Lama)
echo "🗄️ Menjalankan migrasi database baru..."
php artisan migrate --force

# 4. Atur Izin Akses Folder (Permissions)
echo "🔐 Memperbarui izin akses folder..."
chmod -R 777 storage bootstrap/cache database 2>/dev/null
chown -R www:www "$PROJECT_DIR" 2>/dev/null || chown -R www-data:www-data "$PROJECT_DIR" 2>/dev/null

# 5. Refresh & Bersihkan Seluruh Cache Laravel & OPcache
echo "⚡ Perbarui & bersihkan seluruh cache Laravel..."
php artisan optimize:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
php artisan cache:clear

# Reload PHP-FPM di aaPanel untuk reset OPcache
echo "🔄 Reload PHP-FPM & OPcache..."
systemctl reload php-fpm-83 2>/dev/null || systemctl reload php-fpm-82 2>/dev/null || systemctl reload php-fpm-81 2>/dev/null || service php-fpm reload 2>/dev/null || true

echo "======================================================="
echo "🎉 PROSES UPDATE JAGOAN BERHASIL DISLESAIKAN!          "
echo "======================================================="
echo "Aplikasi Anda kini sudah menggunakan versi terbaru."
echo "======================================================="
