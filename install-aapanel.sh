#!/bin/bash

# ==============================================================================
# 🚀 JAGOAN - Automated Installation Script for aaPanel / VPS Linux
# ==============================================================================

echo "======================================================="
echo "   MEMULAI INSTALASI OTOMATIS APLIKASI JAGOAN        "
echo "======================================================="

# Parameters input
# Argument 1: Domain/Subdomain Name (e.g. sub.domain.com) atau Email Admin jika di dalam folder domain
# Argument 2: Email Admin (e.g. admin@email.com)
ARG_1="$1"
ARG_2="$2"

CURRENT_PWD="$(pwd)"
CURRENT_BASENAME="$(basename "$CURRENT_PWD")"

# 1. Tentukan Nama Domain / Subdomain Target
if [ -n "$ARG_1" ] && [[ "$ARG_1" == *"."* ]]; then
    DOMAIN_NAME="$ARG_1"
    ADMIN_EMAIL="${ARG_2:-admin@email.com}"
elif [ "$CURRENT_BASENAME" != "wwwroot" ] && [[ "$CURRENT_BASENAME" == *"."* ]]; then
    DOMAIN_NAME="$CURRENT_BASENAME"
    ADMIN_EMAIL="${ARG_1:-admin@email.com}"
else
    # Jika dijalankan secara interaktif dan belum ada domain, minta input manual
    if [ -t 0 ]; then
        echo -n "📝 Masukkan Nama Domain / Subdomain Anda (contoh: sub.domain.com): "
        read DOMAIN_NAME
        ADMIN_EMAIL="${ARG_1:-admin@email.com}"
    else
        DOMAIN_NAME="$ARG_1"
        ADMIN_EMAIL="${ARG_2:-admin@email.com}"
    fi
fi

# Validasi wajib Nama Domain
if [ -z "$DOMAIN_NAME" ] || [ "$DOMAIN_NAME" == "wwwroot" ]; then
    echo "❌ [SAFETY BLOCKED] Nama domain/subdomain wajib diisi!"
    echo "Penggunaan yang benar:"
    echo "  bash install-aapanel.sh <nama-domain> [email-admin]"
    echo "Contoh:"
    echo "  bash install-aapanel.sh sub.domain.com admin@email.com"
    exit 1
fi

# 2. Penentuan & Penguncian Folder Target Instalasi
TARGET_DIR="/www/wwwroot/$DOMAIN_NAME"

# Buat folder jika belum ada dan masuk ke dalamnya
mkdir -p "$TARGET_DIR"
cd "$TARGET_DIR" || exit 1
PROJECT_DIR="$TARGET_DIR"

# 🔒 PERLINDUNGAN KETAT (SAFETY SHIELD): Pastikan TIDAK BERADA di /www/wwwroot langsung
if [ "$PROJECT_DIR" == "/www/wwwroot" ] || [ "$PROJECT_DIR" == "/www/wwwroot/" ]; then
    echo "❌ [SAFETY SHIELD ABORTED] Script menolak berjalan di root container /www/wwwroot!"
    exit 1
fi

echo "📍 Target Instalasi Terverifikasi Aman:"
echo "   - Domain / Subdomain: $DOMAIN_NAME"
echo "   - Folder Lokasi    : $PROJECT_DIR"

# 3. Bersihkan file bawaan aaPanel di folder target domain
chattr -i .user.ini public/.user.ini 2>/dev/null
rm -f index.html 404.html .user.ini public/.user.ini default 2>/dev/null

# 4. Clone Kode Aplikasi jika belum ada di folder target
if [ ! -f "artisan" ]; then
    echo "📥 Mengunduh / Git Clone kode aplikasi ke $PROJECT_DIR..."
    git init 2>/dev/null
    git remote add origin https://github.com/YogaVanHalen/JAGOAN.git 2>/dev/null || git remote set-url origin https://github.com/YogaVanHalen/JAGOAN.git
    git fetch origin main
    git checkout -B main origin/main -f
fi

echo "📍 Lokasi Aktif Instalasi: $PROJECT_DIR"

# Bypass Composer superuser warning & Git dubious ownership warning
export COMPOSER_ALLOW_SUPERUSER=1
git config --global --add safe.directory "$PROJECT_DIR" 2>/dev/null
git config --global --add safe.directory "*" 2>/dev/null

# 1. Setup File .env & Enable Debug Mode for Troubleshooting
if [ ! -f .env ]; then
    echo "📄 Menyalin file .env.example ke .env..."
    cp .env.example .env
fi

# Pastikan APP_DEBUG=true agar error detail muncul di layar
sed -i 's/APP_DEBUG=false/APP_DEBUG=true/g' .env

# Set Kredensial Database MySQL
echo "⚙️ Mengkonfigurasi Kredensial MySQL (.env)..."
sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=mysql/g' .env
sed -i 's/# DB_HOST=.*/DB_HOST=127.0.0.1/g' .env
sed -i 's/DB_HOST=.*/DB_HOST=127.0.0.1/g' .env
sed -i 's/# DB_PORT=.*/DB_PORT=3306/g' .env
sed -i 's/DB_PORT=.*/DB_PORT=3306/g' .env
sed -i 's/# DB_DATABASE=.*/DB_DATABASE=jagoan/g' .env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=jagoan/g' .env
sed -i 's/# DB_USERNAME=.*/DB_USERNAME=jagoan/g' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=jagoan/g' .env
sed -i 's/# DB_PASSWORD=.*/DB_PASSWORD=k8R6CrryGw3xtjK2/g' .env
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=k8R6CrryGw3xtjK2/g' .env

# 2. Install Dependensi PHP via Composer
echo "📦 Menginstall dependensi Composer (Vendor)..."
if command -v composer &> /dev/null; then
    composer install --no-interaction --prefer-dist --optimize-autoloader
elif [ -f composer.phar ]; then
    php composer.phar install --no-interaction --prefer-dist --optimize-autoloader
else
    echo "❌ Error: Composer tidak ditemukan. Silakan install composer di VPS Anda."
    exit 1
fi

# 3. Generate Application Key
echo "🔑 Membuat Laravel Application Key..."
php artisan key:generate --force

# 4. Database Setup & Migration
echo "🗄️ Menyiapkan Database MySQL & Migrasi Tabel..."
mkdir -p storage/framework/views storage/framework/sessions storage/framework/cache storage/logs
chmod -R 777 storage bootstrap/cache
php artisan migrate:fresh --seed --force

# 5. Konfigurasi Admin Email (Jika Diberikan Parameter)
ADMIN_EMAIL="${1:-admin@email.com}"
echo "👑 Mengatur Akun Admin ke: $ADMIN_EMAIL"
php artisan tinker --execute="App\Models\User::where('email', '$ADMIN_EMAIL')->first()?->update(['role' => 'admin']);"

# 6. Set Permissions & Hapus Pembatasan .user.ini aaPanel
echo "🔐 Mengatur Izin Akses Folder & Hapus Pembatasan .user.ini..."
chattr -i .user.ini public/.user.ini 2>/dev/null
rm -f .user.ini public/.user.ini
chmod -R 777 database storage bootstrap/cache
chown -R www:www "$PROJECT_DIR" 2>/dev/null || chown -R www-data:www-data "$PROJECT_DIR" 2>/dev/null

echo "⚡ Bersihkan Cache Laravel..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 7. Otomatisasi Konfigurasi Web Server aaPanel (Nginx / Apache / OpenLiteSpeed)
SITE_DOMAIN="$(basename "$PROJECT_DIR")"
NGINX_VHOST="/www/server/panel/vhost/nginx/${SITE_DOMAIN}.conf"
NGINX_REWRITE="/www/server/panel/vhost/rewrite/${SITE_DOMAIN}.conf"
APACHE_VHOST="/www/server/panel/vhost/apache/${SITE_DOMAIN}.conf"
OLS_VHOST="/www/server/panel/vhost/openlitespeed/${SITE_DOMAIN}.conf"

echo "🌐 Memeriksa & Mengonfigurasi Web Server aaPanel..."

# Konfigurasi Nginx
if [ -f "$NGINX_VHOST" ]; then
    echo "🔧 Mengatur Nginx Running Directory ke /public & Laravel Rewrite..."
    sed -i "s|root $PROJECT_DIR;|root $PROJECT_DIR/public;|g" "$NGINX_VHOST" 2>/dev/null
    sed -i "s|root $PROJECT_DIR/;|root $PROJECT_DIR/public;|g" "$NGINX_VHOST" 2>/dev/null
    
    if [ -f "$NGINX_REWRITE" ]; then
        echo -e "location / {\n    try_files \$uri \$uri/ /index.php?\$query_string;\n}" > "$NGINX_REWRITE" 2>/dev/null
    fi
    nginx -s reload 2>/dev/null || systemctl reload nginx 2>/dev/null || service nginx reload 2>/dev/null || true
fi

# Konfigurasi Apache
if [ -f "$APACHE_VHOST" ]; then
    echo "🔧 Mengatur Apache DocumentRoot ke /public..."
    sed -i "s|DocumentRoot \"$PROJECT_DIR\"|DocumentRoot \"$PROJECT_DIR/public\"|g" "$APACHE_VHOST" 2>/dev/null
    sed -i "s|<Directory \"$PROJECT_DIR\">|<Directory \"$PROJECT_DIR/public\">|g" "$APACHE_VHOST" 2>/dev/null
    systemctl reload httpd 2>/dev/null || systemctl reload apache2 2>/dev/null || service httpd reload 2>/dev/null || true
fi

# Konfigurasi OpenLiteSpeed
if [ -f "$OLS_VHOST" ]; then
    echo "🔧 Mengatur OpenLiteSpeed docRoot ke /public..."
    sed -i "s|docRoot                   $PROJECT_DIR|docRoot                   $PROJECT_DIR/public|g" "$OLS_VHOST" 2>/dev/null
    /usr/local/lsws/bin/lswsctrl reload 2>/dev/null || systemctl reload lsws 2>/dev/null || true
fi

echo "======================================================="
echo "🎉 INSTALASI JAGOAN BERHASIL DISLESAIKAN!             "
echo "======================================================="
echo "⚠️  PENGATURAN TERAKHIR DI AAPANEL (JIKA BELUM OTOMATIS):"
echo " 1. Masuk ke aaPanel -> Website -> Klik Domain Anda"
echo " 2. Di tab 'Site directory': Set Site directory ke: $PROJECT_DIR"
echo " 3. Di tab 'Site directory': Set Running directory ke: /public"
echo " 4. Di tab 'Site directory': Hapus centang 'Anti-user-site-executive (open_basedir)'"
echo " 5. Di tab 'URL rewrite': Pilih preset 'laravel'"
echo " 6. Buka domain Anda di browser untuk menggunakan aplikasi!"
echo "======================================================="
