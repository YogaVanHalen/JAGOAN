#!/bin/bash

# ==============================================================================
# 🚀 JAGOAN - Automated Installation Script for aaPanel / VPS Linux
# ==============================================================================

echo "======================================================="
echo "   MEMULAI INSTALASI OTOMATIS APLIKASI JAGOAN        "
echo "======================================================="

# Dapatkan lokasi script saat ini
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CURRENT_FOLDER="$(basename "$SCRIPT_DIR")"

# Jika script dijalankan dari dalam subfolder "JAGOAN", pindahkan semua file 1 tingkat ke atas (Root Domain)
if [ "$CURRENT_FOLDER" == "JAGOAN" ] || [ "$CURRENT_FOLDER" == "jagoan" ]; then
    PARENT_DIR="$(dirname "$SCRIPT_DIR")"
    echo "📁 Mendeteksi folder '$CURRENT_FOLDER'. Memindahkan seluruh isi ke Root Domain ($PARENT_DIR)..."
    
    # Aktifkan dotglob untuk ikut memindahkan file tersembunyi seperti .env dan .git
    shopt -s dotglob
    mv "$SCRIPT_DIR"/* "$PARENT_DIR"/ 2>/dev/null
    shopt -u dotglob
    
    cd "$PARENT_DIR" || exit 1
    rm -rf "$SCRIPT_DIR" 2>/dev/null
    PROJECT_DIR="$PARENT_DIR"
else
    cd "$SCRIPT_DIR" || exit 1
    PROJECT_DIR="$SCRIPT_DIR"
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

echo "======================================================="
echo "🎉 INSTALASI JAGOAN BERHASIL DISLESAIKAN!             "
echo "======================================================="
echo "⚠️  PENGATURAN TERAKHIR DI AAPANEL:"
echo " 1. Masuk ke aaPanel -> Website -> Klik Domain Anda"
echo " 2. Di tab 'Site directory': Set Site directory ke: $PROJECT_DIR"
echo " 3. Di tab 'Site directory': Set Running directory ke: /public"
echo " 4. Di tab 'URL rewrite': Pilih preset 'laravel'"
echo " 5. Buka domain Anda di browser untuk menggunakan aplikasi!"
echo "======================================================="
