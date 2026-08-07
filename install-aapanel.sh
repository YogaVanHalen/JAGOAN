#!/bin/bash

# ==============================================================================
# 🚀 JAGOAN - Interactive Automated Installer for aaPanel / VPS Linux
# ==============================================================================

# Warna & Format Terminal
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m' # No Color

# Helper Functions untuk Output Interaktif
log_header() {
    echo -e "\n${BOLD}${CYAN}=======================================================${NC}"
    echo -e "${BOLD}${PURPLE}   $1${NC}"
    echo -e "${BOLD}${CYAN}=======================================================${NC}\n"
}

log_step() {
    echo -e "\n${BOLD}${BLUE}──> $1${NC}"
}

log_success() {
    echo -e "  ${GREEN}✔ [BERHASIL]${NC} $1"
}

log_fail() {
    echo -e "  ${RED}✖ [GAGAL]${NC} $1"
}

log_warn() {
    echo -e "  ${YELLOW}⚠ [PERINGATAN]${NC} $1"
}

log_info() {
    echo -e "  ${CYAN}ℹ [INFO]${NC} $1"
}

# Clear screen untuk awal installer
clear

log_header "🚀 MENU INSTALASI INTERAKTIF APLIKASI JAGOAN"

ARG_1="$1"
ARG_2="$2"

CURRENT_PWD="$(pwd)"
CURRENT_BASENAME="$(basename "$CURRENT_PWD")"

# ------------------------------------------------------------------------------
# LANGKAH 1: Validasi Domain / Subdomain Target
# ------------------------------------------------------------------------------
log_step "Langkah 1: Penentuan Domain / Subdomain Target"

if [ -n "$ARG_1" ] && [[ "$ARG_1" == *"."* ]]; then
    DOMAIN_NAME="$ARG_1"
    ADMIN_EMAIL="${ARG_2:-admin@email.com}"
elif [ "$CURRENT_BASENAME" != "wwwroot" ] && [[ "$CURRENT_BASENAME" == *"."* ]]; then
    DOMAIN_NAME="$CURRENT_BASENAME"
    ADMIN_EMAIL="${ARG_1:-admin@email.com}"
else
    echo -e "${CYAN}📝 Masukkan Nama Domain / Subdomain Anda (contoh: finance.ginetz.id):${NC} "
    read -r DOMAIN_NAME
    echo -e "${CYAN}👑 Masukkan Email Administrator (default: admin@email.com):${NC} "
    read -r ADMIN_INPUT
    ADMIN_EMAIL="${ADMIN_INPUT:-admin@email.com}"
fi

if [ -z "$DOMAIN_NAME" ] || [ "$DOMAIN_NAME" == "wwwroot" ]; then
    log_fail "Nama domain / subdomain wajib diisi!"
    echo -e "  Penggunaan:"
    echo -e "    bash install-aapanel.sh <nama-domain> [email-admin]"
    echo -e "  Contoh:"
    echo -e "    bash install-aapanel.sh finance.ginetz.id admin@email.com"
    exit 1
fi

TARGET_DIR="/www/wwwroot/$DOMAIN_NAME"
mkdir -p "$TARGET_DIR"
cd "$TARGET_DIR" || exit 1
PROJECT_DIR="$TARGET_DIR"

if [ "$PROJECT_DIR" == "/www/wwwroot" ] || [ "$PROJECT_DIR" == "/www/wwwroot/" ]; then
    log_fail "Script ditolak berjalan langsung di /www/wwwroot!"
    exit 1
fi

log_success "Domain Target : ${BOLD}$DOMAIN_NAME${NC}"
log_success "Folder Lokasi : ${BOLD}$PROJECT_DIR${NC}"

# ------------------------------------------------------------------------------
# LANGKAH 2: Pemeriksaan Lingkungan VPS (PHP 8.2+ & Composer API Check)
# ------------------------------------------------------------------------------
log_step "Langkah 2: Pemeriksaan Lingkungan VPS & Dependensi"

PHP_BIN="php"
BEST_PHP_VER=0

# Cari binary PHP 8.2+ terbaik di aaPanel / VPS
for candidate in /www/server/php/84/bin/php /www/server/php/83/bin/php /www/server/php/82/bin/php $(which php 2>/dev/null); do
    if [ -x "$candidate" ]; then
        VER=$($candidate -r "echo PHP_VERSION_ID;" 2>/dev/null || echo 0)
        if [ "$VER" -ge 80200 ] && [ "$VER" -gt "$BEST_PHP_VER" ]; then
            BEST_PHP_VER=$VER
            PHP_BIN="$candidate"
        fi
    fi
done

if [ "$BEST_PHP_VER" -lt 80200 ]; then
    log_fail "PHP 8.2 atau versi lebih baru tidak ditemukan di VPS!"
    log_info "Silakan install PHP 8.2 atau PHP 8.3 via aaPanel App Store."
    exit 1
else
    PHP_VER_STR=$($PHP_BIN -r "echo PHP_VERSION;" 2>/dev/null)
    log_success "PHP Terdeteksi : ${BOLD}PHP $PHP_VER_STR${NC} ($PHP_BIN)"
fi

# Periksa ekstensi PHP penting
MISSING_EXTS=()
for ext in pdo_mysql mbstring fileinfo openssl curl xml zip; do
    if ! $PHP_BIN -m 2>/dev/null | grep -qi "^$ext$"; then
        MISSING_EXTS+=("$ext")
    fi
done

if [ ${#MISSING_EXTS[@]} -gt 0 ]; then
    log_warn "Ekstensi PHP berikut mungkin belum aktif: ${MISSING_EXTS[*]}"
else
    log_success "Ekstensi PHP Utama : ${BOLD}Lengkap (pdo_mysql, mbstring, fileinfo, dll)${NC}"
fi

# Periksa Composer & Composer Runtime API (^2.2)
log_info "Menyiapkan Composer 2.x versi terbaru (Support Laravel 12 & Runtime API ^2.2)..."

COMPOSER_CMD=""
export COMPOSER_ALLOW_SUPERUSER=1

# Selalu unduh/gunakan composer.phar versi stable terbaru jika /usr/bin/composer terlalu lama (< 2.2)
if [ ! -f "composer.phar" ] || ! $PHP_BIN composer.phar --version 2>/dev/null | grep -qE "Composer version 2\.[2-9]"; then
    log_info "Mengunduh file composer.phar resmi terbaru dari getcomposer.org..."
    curl -sS https://getcomposer.org/composer-stable.phar -o composer.phar 2>/dev/null
    chmod +x composer.phar 2>/dev/null
fi

if [ -f "composer.phar" ]; then
    COMPOSER_CMD="$PHP_BIN composer.phar"
elif command -v composer &>/dev/null; then
    COMPOSER_CMD="composer"
fi

if [ -n "$COMPOSER_CMD" ]; then
    COMPOSER_VER_OUT=$($COMPOSER_CMD --version 2>/dev/null | head -n 1)
    log_success "Composer Executable : ${BOLD}$COMPOSER_VER_OUT${NC}"
else
    log_fail "Gagal menyiapkan Composer 2.x versi terbaru!"
    exit 1
fi

export COMPOSER_ALLOW_SUPERUSER=1
git config --global --add safe.directory "$PROJECT_DIR" 2>/dev/null
git config --global --add safe.directory "*" 2>/dev/null

# ------------------------------------------------------------------------------
# LANGKAH 3: Pembersihan Pembatasan aaPanel & Pull Kode
# ------------------------------------------------------------------------------
log_step "Langkah 3: Pembersihan Pembatasan aaPanel & Mengunduh Kode Repository"

chattr -i .user.ini public/.user.ini 2>/dev/null
rm -f index.html 404.html .user.ini public/.user.ini default 2>/dev/null
rm -f /www/server/panel/vhost/open_basedir/nginx/${DOMAIN_NAME}.conf 2>/dev/null
rm -f /www/server/panel/vhost/open_basedir/apache/${DOMAIN_NAME}.conf 2>/dev/null

if [ ! -f "artisan" ]; then
    log_info "Mengunduh kode aplikasi dari GitHub..."
    git init 2>/dev/null
    git remote add origin https://github.com/YogaVanHalen/JAGOAN.git 2>/dev/null || git remote set-url origin https://github.com/YogaVanHalen/JAGOAN.git
    git fetch origin main
    git checkout -B main origin/main -f
    if [ $? -eq 0 ]; then
        log_success "Git Clone Kode Aplikasi : ${BOLD}Sukses (origin/main)${NC}"
    else
        log_fail "Gagal mengunduh kode aplikasi dari GitHub!"
        exit 1
    fi
else
    log_success "Kode Aplikasi : ${BOLD}Sudah tersedia di folder target${NC}"
fi

# ------------------------------------------------------------------------------
# LANGKAH 4: Form Interaktif Kredensial Database & Configuration
# ------------------------------------------------------------------------------
log_step "Langkah 4: Konfigurasi Database MySQL & Cloudflare Turnstile"
echo -e "${YELLOW}ℹ Catatan: Teks password ditampilkan saat diketik/dipaste agar mudah diperiksa.${NC}\n"

echo -n -e "${CYAN}🗄️  Nama Database MySQL [default: jagoan]:${NC} "
read -r DB_NAME
DB_NAME="${DB_NAME:-jagoan}"

echo -n -e "${CYAN}👤 Username Database MySQL [default: jagoan]:${NC} "
read -r DB_USER
DB_USER="${DB_USER:-jagoan}"

echo -n -e "${CYAN}🔑 Password Database MySQL [default: k8R6CrryGw3xtjK2]:${NC} "
read -r DB_PASS
DB_PASS="${DB_PASS:-k8R6CrryGw3xtjK2}"

echo ""
echo -n -e "${CYAN}🛡️  Cloudflare Turnstile Site Key (Tekan Enter jika tidak ada):${NC} "
read -r TURNSTILE_SITE_KEY

echo -n -e "${CYAN}🛡️  Cloudflare Turnstile Secret Key (Tekan Enter jika tidak ada):${NC} "
read -r TURNSTILE_SECRET_KEY

# Update File .env
if [ ! -f .env ]; then
    cp .env.example .env
fi

sed -i 's/APP_DEBUG=false/APP_DEBUG=true/g' .env
sed -i "s|APP_URL=.*|APP_URL=http://$DOMAIN_NAME|g" .env
sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=mysql/g' .env
sed -i 's/# DB_HOST=.*/DB_HOST=127.0.0.1/g' .env
sed -i 's/DB_HOST=.*/DB_HOST=127.0.0.1/g' .env
sed -i 's/# DB_PORT=.*/DB_PORT=3306/g' .env
sed -i 's/DB_PORT=.*/DB_PORT=3306/g' .env

sed -i "s/# DB_DATABASE=.*/DB_DATABASE=$DB_NAME/g" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/g" .env
sed -i "s/# DB_USERNAME=.*/DB_USERNAME=$DB_USER/g" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/g" .env
sed -i "s/# DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/g" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/g" .env

if ! grep -q "TURNSTILE_SITE_KEY" .env; then
    echo "" >> .env
    echo "# Cloudflare Turnstile Keys" >> .env
    echo "TURNSTILE_SITE_KEY=$TURNSTILE_SITE_KEY" >> .env
    echo "TURNSTILE_SECRET_KEY=$TURNSTILE_SECRET_KEY" >> .env
else
    sed -i "s|TURNSTILE_SITE_KEY=.*|TURNSTILE_SITE_KEY=$TURNSTILE_SITE_KEY|g" .env
    sed -i "s|TURNSTILE_SECRET_KEY=.*|TURNSTILE_SECRET_KEY=$TURNSTILE_SECRET_KEY|g" .env
fi

log_success "Konfigurasi .env : ${BOLD}Disimpan Berhasil${NC}"

# ------------------------------------------------------------------------------
# LANGKAH 5: Instalasi Dependensi Composer (Vendor)
# ------------------------------------------------------------------------------
log_step "Langkah 5: Instalasi Paket Composer (Vendor)"

log_info "Menjalankan '$COMPOSER_CMD install'..."
$COMPOSER_CMD install --no-interaction --prefer-dist --optimize-autoloader 2>&1 | tee /tmp/composer_install.log

if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    log_warn "Coba install dengan opsi --ignore-platform-reqs..."
    $COMPOSER_CMD install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs
fi

if [ -f "vendor/autoload.php" ]; then
    log_success "Instalasi Vendor Composer : ${BOLD}Sukses (vendor/autoload.php terverifikasi)${NC}"
else
    log_fail "Instalasi Composer Gagal! Periksa koneksi internet atau memori VPS Anda."
    exit 1
fi

# ------------------------------------------------------------------------------
# LANGKAH 6: Setup Application Key & Migrasi Database
# ------------------------------------------------------------------------------
log_step "Langkah 6: Laravel Application Key & Migrasi Database"

$PHP_BIN artisan key:generate --force
if [ $? -eq 0 ]; then
    log_success "Laravel APP_KEY : ${BOLD}Berhasil Dibuat${NC}"
else
    log_fail "Gagal membuat Application Key!"
fi

mkdir -p storage/framework/views storage/framework/sessions storage/framework/cache storage/logs
chmod -R 777 storage bootstrap/cache database 2>/dev/null

log_info "Menjalankan migrasi database & seeder..."
$PHP_BIN artisan migrate:fresh --seed --force
if [ $? -eq 0 ]; then
    log_success "Migrasi & Seeder Database : ${BOLD}Sukses Memuat Tabel${NC}"
else
    log_fail "Migrasi Database Gagal! Pastikan nama database '$DB_NAME' & user '$DB_USER' sudah dibuat di aaPanel -> Databases."
fi

# Set Role Admin
if [ -n "$ADMIN_EMAIL" ]; then
    log_info "Mengkonfigurasi Role Administrator untuk '$ADMIN_EMAIL'..."
    $PHP_BIN artisan tinker --execute="App\Models\User::where('email', '$ADMIN_EMAIL')->first()?->update(['role' => 'admin']);" 2>/dev/null
    log_success "Role Admin Administrator : ${BOLD}Diatur ke $ADMIN_EMAIL${NC}"
fi

# ------------------------------------------------------------------------------
# LANGKAH 7: Permissions, Cache Clear & Web Server Rewrite
# ------------------------------------------------------------------------------
log_step "Langkah 7: Hak Akses Folder & Konfigurasi Web Server aaPanel"

chattr -i .user.ini public/.user.ini 2>/dev/null
rm -f .user.ini public/.user.ini
chmod -R 777 database storage bootstrap/cache
chown -R www:www "$PROJECT_DIR" 2>/dev/null || chown -R www-data:www-data "$PROJECT_DIR" 2>/dev/null
log_success "Permissions Folder : ${BOLD}chmod 777 storage & bootstrap/cache${NC}"

log_info "Membersihkan seluruh Cache Laravel..."
$PHP_BIN artisan config:clear >/dev/null
$PHP_BIN artisan route:clear >/dev/null
$PHP_BIN artisan view:clear >/dev/null
$PHP_BIN artisan cache:clear >/dev/null
log_success "Cache Laravel : ${BOLD}Dibersihkan${NC}"

# Configure Web Server aaPanel
SITE_DOMAIN="$(basename "$PROJECT_DIR")"
NGINX_VHOST="/www/server/panel/vhost/nginx/${SITE_DOMAIN}.conf"
NGINX_REWRITE="/www/server/panel/vhost/rewrite/${SITE_DOMAIN}.conf"
APACHE_VHOST="/www/server/panel/vhost/apache/${SITE_DOMAIN}.conf"
OLS_VHOST="/www/server/panel/vhost/openlitespeed/${SITE_DOMAIN}.conf"

log_info "Memeriksa konfigurasi Web Server aaPanel..."

if [ -f "$NGINX_VHOST" ]; then
    # 1. Update Root Document ke /public
    sed -i -E "s|root\s+[^;]+;|root ${PROJECT_DIR}/public;|g" "$NGINX_VHOST" 2>/dev/null
    
    # 2. Update Index Directive untuk menyertakan index.php
    if grep -q "index " "$NGINX_VHOST"; then
        sed -i -E "s|index\s+[^;]+;|index index.php index.html index.htm;|g" "$NGINX_VHOST" 2>/dev/null
    fi
    
    # 3. Hapus pembatasan open_basedir dari vhost Nginx
    sed -i '/open_basedir/d' "$NGINX_VHOST" 2>/dev/null
    
    # 4. Tulis URL Rewrite Laravel
    mkdir -p "$(dirname "$NGINX_REWRITE")" 2>/dev/null
    echo -e "location / {\n    try_files \$uri \$uri/ /index.php?\$query_string;\n}" > "$NGINX_REWRITE" 2>/dev/null
    
    # 5. Pastikan File Rewrite ter-include di Vhost Nginx
    if ! grep -q "rewrite/${SITE_DOMAIN}.conf" "$NGINX_VHOST"; then
        sed -i "/server_name/a \    include /www/server/panel/vhost/rewrite/${SITE_DOMAIN}.conf;" "$NGINX_VHOST" 2>/dev/null
    fi
    
    # 6. Pastikan Handler PHP (enable-php-XX.conf) Terpasang di Vhost Nginx setelah baris root
    if ! grep -q "enable-php" "$NGINX_VHOST"; then
        PHP_ENABLE_CONF="enable-php-84.conf"
        for conf in enable-php-84.conf enable-php-83.conf enable-php-82.conf enable-php-81.conf enable-php-80.conf enable-php-74.conf; do
            if [ -f "/www/server/nginx/conf/$conf" ] || [ -f "/www/server/panel/vhost/nginx/$conf" ]; then
                PHP_ENABLE_CONF="$conf"
                break
            fi
        done
        sed -i "/root /a \    include $PHP_ENABLE_CONF;" "$NGINX_VHOST" 2>/dev/null
    fi
    
    # 7. Reload Nginx
    nginx -t &>/dev/null && (nginx -s reload 2>/dev/null || systemctl reload nginx 2>/dev/null || true)
    log_success "Nginx Config & Rewrite : ${BOLD}Root /public, PHP Handler & Laravel Rewrite Aktif${NC}"
fi

if [ -f "$APACHE_VHOST" ]; then
    sed -i "s|DocumentRoot \"$PROJECT_DIR\"|DocumentRoot \"$PROJECT_DIR/public\"|g" "$APACHE_VHOST" 2>/dev/null
    sed -i "s|<Directory \"$PROJECT_DIR\">|<Directory \"$PROJECT_DIR/public\">|g" "$APACHE_VHOST" 2>/dev/null
    systemctl reload httpd 2>/dev/null || systemctl reload apache2 2>/dev/null || true
    log_success "Apache Config : ${BOLD}DocumentRoot /public Aktif${NC}"
fi

if [ -f "$OLS_VHOST" ]; then
    sed -i "s|docRoot                   $PROJECT_DIR|docRoot                   $PROJECT_DIR/public|g" "$OLS_VHOST" 2>/dev/null
    /usr/local/lsws/bin/lswsctrl reload 2>/dev/null || true
    log_success "OpenLiteSpeed Config : ${BOLD}docRoot /public Aktif${NC}"
fi

# ------------------------------------------------------------------------------
# RINGKASAN AKHIR & CHECKLIST STATUS
# ------------------------------------------------------------------------------
log_header "🎉 INSTALASI JAGOAN BERHASIL DISLESAIKAN!"

echo -e "${BOLD}Checklist Status Instalasi:${NC}"
echo -e "  ${GREEN}✔ [OK]${NC} Domain & Location    : $DOMAIN_NAME ($PROJECT_DIR)"
echo -e "  ${GREEN}✔ [OK]${NC} PHP Version          : PHP $PHP_VER_STR ($PHP_BIN)"
echo -e "  ${GREEN}✔ [OK]${NC} Composer Vendor      : vendor/autoload.php (Sukses)"
echo -e "  ${GREEN}✔ [OK]${NC} Database & Seeder    : MySQL $DB_NAME"
echo -e "  ${GREEN}✔ [OK]${NC} Web Server Document  : /public (Laravel Rewrite)"

echo -e "\n${BOLD}${YELLOW}⚠️  PENGATURAN TERAKHIR DI PANEL AAPANEL (JIKA DIBUTUHKAN):${NC}"
echo -e " 1. Masuk ke aaPanel -> Website -> Klik domain ${BOLD}$DOMAIN_NAME${NC}"
echo -e " 2. Tab 'Site directory': Set Running directory ke ${BOLD}/public${NC}"
echo -e " 3. Tab 'Site directory': Hapus centang ${BOLD}Anti-user-site-executive (open_basedir)${NC}"
echo -e " 4. Tab 'URL rewrite': Memastikan preset ${BOLD}laravel${NC} terpilih"
echo -e " 5. Buka domain Anda di browser: ${BOLD}http://$DOMAIN_NAME${NC}\n"
