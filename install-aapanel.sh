#!/bin/bash

# ==============================================================================
# 🚀 JAGOAN - Interactive Automated Installer for aaPanel / VPS Linux
# ==============================================================================
# Versi : 2.0.0 (Production-Ready)
# Penulis: YogaVanHalen
# Catatan: Script ini dirancang untuk deploy pertama kali di server baru.
#          Untuk update/pembaruan, gunakan update-aapanel.sh
# ==============================================================================

set -euo pipefail

# Warna & Format Terminal
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m'

# Status Tracking (0=sukses, 1=gagal, 2=skip)
declare -A STATUS
STATUS[php]=2
STATUS[composer]=2
STATUS[git]=2
STATUS[env]=2
STATUS[vendor]=2
STATUS[appkey]=2
STATUS[migrate]=2
STATUS[permissions]=2
STATUS[nginx]=2

# Helper Functions
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

print_status_line() {
    local key="$1"
    local label="$2"
    local detail="$3"
    case "${STATUS[$key]}" in
        0) echo -e "  ${GREEN}✔ [OK]${NC}     ${label} : ${detail}" ;;
        1) echo -e "  ${RED}✖ [GAGAL]${NC}  ${label} : ${detail}" ;;
        2) echo -e "  ${YELLOW}○ [SKIP]${NC}  ${label} : ${detail}" ;;
    esac
}

clear

log_header "🚀 MENU INSTALASI INTERAKTIF APLIKASI JAGOAN v2.0"

ARG_1="${1:-}"
ARG_2="${2:-}"

CURRENT_PWD="$(pwd)"
CURRENT_BASENAME="$(basename "$CURRENT_PWD")"

# ==============================================================================
# LANGKAH 1: Validasi Domain / Subdomain Target
# ==============================================================================
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

if [ -z "${DOMAIN_NAME:-}" ] || [ "$DOMAIN_NAME" == "wwwroot" ]; then
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

# ==============================================================================
# LANGKAH 2: Deteksi PHP 8.2+ & Composer 2.x
# ==============================================================================
log_step "Langkah 2: Pemeriksaan Lingkungan VPS & Dependensi"

PHP_BIN="php"
BEST_PHP_VER=0
PHP_VER_STR="N/A"

# Scan semua binary PHP aaPanel (84 -> 83 -> 82) dan system php
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
    log_info "Silakan install PHP 8.2+ via aaPanel → App Store → Runtime → PHP."
    STATUS[php]=1
    exit 1
else
    PHP_VER_STR=$($PHP_BIN -r "echo PHP_VERSION;" 2>/dev/null)
    log_success "PHP Terdeteksi : ${BOLD}PHP $PHP_VER_STR${NC} ($PHP_BIN)"
    STATUS[php]=0
fi

# Tentukan versi PHP numerik (84, 83, 82) untuk Nginx enable-php
PHP_MAJOR_MINOR=$($PHP_BIN -r "echo PHP_MAJOR_VERSION . PHP_MINOR_VERSION;" 2>/dev/null || echo "84")

# Periksa ekstensi PHP penting
MISSING_EXTS=()
for ext in pdo_mysql mbstring fileinfo openssl curl xml zip; do
    if ! $PHP_BIN -m 2>/dev/null | grep -qi "^${ext}$"; then
        MISSING_EXTS+=("$ext")
    fi
done

if [ ${#MISSING_EXTS[@]} -gt 0 ]; then
    log_warn "Ekstensi PHP berikut mungkin belum aktif: ${MISSING_EXTS[*]}"
    log_info "Install via aaPanel → App Store → PHP $PHP_VER_STR → Settings → Extensions"
else
    log_success "Ekstensi PHP Utama : ${BOLD}Lengkap (pdo_mysql, mbstring, fileinfo, dll)${NC}"
fi

# Selalu download/gunakan Composer 2.x versi terbaru via composer.phar
log_info "Menyiapkan Composer 2.x versi terbaru..."

COMPOSER_CMD=""
export COMPOSER_ALLOW_SUPERUSER=1

# Download composer.phar jika belum ada atau versi lama (< 2.2)
NEED_DOWNLOAD=0
if [ ! -f "composer.phar" ]; then
    NEED_DOWNLOAD=1
else
    COMPOSER_API_VER=$($PHP_BIN composer.phar --version 2>/dev/null | grep -oE '2\.[0-9]+\.[0-9]+' | head -1 || echo "0.0.0")
    COMPOSER_MINOR=$(echo "$COMPOSER_API_VER" | cut -d. -f2)
    if [ "${COMPOSER_MINOR:-0}" -lt 2 ]; then
        NEED_DOWNLOAD=1
    fi
fi

if [ "$NEED_DOWNLOAD" -eq 1 ]; then
    log_info "Mengunduh composer.phar resmi terbaru dari getcomposer.org..."
    curl -sS https://getcomposer.org/composer-stable.phar -o composer.phar 2>/dev/null
    chmod +x composer.phar 2>/dev/null
fi

if [ -f "composer.phar" ]; then
    COMPOSER_CMD="$PHP_BIN composer.phar"
elif command -v composer &>/dev/null; then
    # Fallback ke system composer hanya jika versi cukup baru
    SYS_COMPOSER_VER=$(composer --version 2>/dev/null | grep -oE '2\.[0-9]+' | head -1 || echo "0.0")
    SYS_MINOR=$(echo "$SYS_COMPOSER_VER" | cut -d. -f2)
    if [ "${SYS_MINOR:-0}" -ge 2 ]; then
        COMPOSER_CMD="composer"
    fi
fi

if [ -n "$COMPOSER_CMD" ]; then
    COMPOSER_VER_OUT=$($COMPOSER_CMD --version 2>/dev/null | head -n 1)
    log_success "Composer Ready : ${BOLD}$COMPOSER_VER_OUT${NC}"
    STATUS[composer]=0
else
    log_fail "Gagal menyiapkan Composer 2.2+! Cek koneksi internet."
    STATUS[composer]=1
    exit 1
fi

git config --global --add safe.directory "$PROJECT_DIR" 2>/dev/null
git config --global --add safe.directory "*" 2>/dev/null

# ==============================================================================
# LANGKAH 3: Pembersihan Default aaPanel & Clone Kode
# ==============================================================================
log_step "Langkah 3: Pembersihan Default aaPanel & Mengunduh Kode Repository"

# Hapus semua pembatasan aaPanel
chattr -i .user.ini public/.user.ini 2>/dev/null || true
rm -f index.html 404.html .user.ini public/.user.ini default 2>/dev/null
rm -f "/www/server/panel/vhost/open_basedir/nginx/${DOMAIN_NAME}.conf" 2>/dev/null
rm -f "/www/server/panel/vhost/open_basedir/apache/${DOMAIN_NAME}.conf" 2>/dev/null
log_success "Pembatasan aaPanel : ${BOLD}Dibersihkan (open_basedir, .user.ini)${NC}"

if [ ! -f "artisan" ]; then
    log_info "Mengunduh kode aplikasi dari GitHub..."
    git init 2>/dev/null
    git remote add origin https://github.com/YogaVanHalen/JAGOAN.git 2>/dev/null || git remote set-url origin https://github.com/YogaVanHalen/JAGOAN.git
    if git fetch origin main && git checkout -B main origin/main -f; then
        log_success "Git Clone Kode : ${BOLD}Sukses (origin/main)${NC}"
        STATUS[git]=0
    else
        log_fail "Gagal mengunduh kode aplikasi dari GitHub!"
        STATUS[git]=1
        exit 1
    fi
else
    log_success "Kode Aplikasi : ${BOLD}Sudah tersedia di folder target${NC}"
    STATUS[git]=0
fi

# ==============================================================================
# LANGKAH 4: Konfigurasi Database & Environment (.env)
# ==============================================================================
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

# Tulis .env baru menggunakan heredoc (tidak pakai sed yang rawan error)
cat > .env << ENVEOF
APP_NAME=JAGOAN
APP_ENV=production
APP_KEY=
APP_DEBUG=true
APP_URL=http://$DOMAIN_NAME

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=$DB_NAME
DB_USERNAME=$DB_USER
DB_PASSWORD=$DB_PASS

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="\${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="\${APP_NAME}"

TURNSTILE_SITE_KEY=$TURNSTILE_SITE_KEY
TURNSTILE_SECRET_KEY=$TURNSTILE_SECRET_KEY
ENVEOF

log_success "Konfigurasi .env : ${BOLD}Disimpan (DB=$DB_NAME, URL=http://$DOMAIN_NAME)${NC}"
STATUS[env]=0

# ==============================================================================
# LANGKAH 5: Instalasi Dependensi Composer (Vendor)
# ==============================================================================
log_step "Langkah 5: Instalasi Paket Composer (Vendor)"

log_info "Menjalankan '$COMPOSER_CMD install'..."
$COMPOSER_CMD install --no-interaction --prefer-dist --optimize-autoloader 2>&1 | tail -n 5

if [ ! -f "vendor/autoload.php" ]; then
    log_warn "Retry dengan --ignore-platform-reqs..."
    $COMPOSER_CMD install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs 2>&1 | tail -n 5
fi

if [ -f "vendor/autoload.php" ]; then
    log_success "Vendor Composer : ${BOLD}Sukses (vendor/autoload.php terverifikasi)${NC}"
    STATUS[vendor]=0
else
    log_fail "Instalasi Composer Gagal! Periksa koneksi internet atau memori VPS."
    STATUS[vendor]=1
    exit 1
fi

# ==============================================================================
# LANGKAH 6: Setup Application Key & Migrasi Database
# ==============================================================================
log_step "Langkah 6: Laravel Application Key & Migrasi Database"

# Buat direktori storage lengkap sebelum artisan commands
mkdir -p storage/framework/views storage/framework/sessions storage/framework/cache storage/logs
chmod -R 777 storage bootstrap/cache database 2>/dev/null

if $PHP_BIN artisan key:generate --force 2>/dev/null; then
    log_success "Laravel APP_KEY : ${BOLD}Berhasil Dibuat${NC}"
    STATUS[appkey]=0
else
    log_fail "Gagal membuat Application Key!"
    STATUS[appkey]=1
fi

log_info "Menjalankan migrasi database & seeder..."
if $PHP_BIN artisan migrate:fresh --seed --force 2>&1 | tail -n 5; then
    log_success "Migrasi & Seeder : ${BOLD}Sukses Memuat Tabel${NC}"
    STATUS[migrate]=0
else
    log_fail "Migrasi Database Gagal! Pastikan:"
    echo -e "    1. Database '${BOLD}$DB_NAME${NC}' sudah dibuat di aaPanel → Databases"
    echo -e "    2. User '${BOLD}$DB_USER${NC}' sudah dibuat dengan akses ke database '${BOLD}$DB_NAME${NC}'"
    echo -e "    3. Password benar: ${BOLD}$DB_PASS${NC}"
    STATUS[migrate]=1
fi

# Set Role Admin
if [ -n "${ADMIN_EMAIL:-}" ]; then
    log_info "Mengkonfigurasi Role Administrator untuk '$ADMIN_EMAIL'..."
    $PHP_BIN artisan tinker --execute="App\Models\User::where('email', '$ADMIN_EMAIL')->first()?->update(['role' => 'admin']);" 2>/dev/null || true
    log_success "Role Admin : ${BOLD}Diatur ke $ADMIN_EMAIL${NC}"
fi

# ==============================================================================
# LANGKAH 7: Permissions, Cache & Konfigurasi Web Server
# ==============================================================================
log_step "Langkah 7: Hak Akses, Cache & Konfigurasi Web Server aaPanel"

# Pembersihan ulang .user.ini (kadang aaPanel re-create setelah reload)
chattr -i .user.ini public/.user.ini 2>/dev/null || true
rm -f .user.ini public/.user.ini

chmod -R 777 database storage bootstrap/cache
chown -R www:www "$PROJECT_DIR" 2>/dev/null || chown -R www-data:www-data "$PROJECT_DIR" 2>/dev/null
log_success "Permissions Folder : ${BOLD}chmod 777 storage & bootstrap/cache${NC}"
STATUS[permissions]=0

log_info "Membersihkan seluruh Cache Laravel..."
$PHP_BIN artisan config:clear >/dev/null 2>&1 || true
$PHP_BIN artisan route:clear >/dev/null 2>&1 || true
$PHP_BIN artisan view:clear >/dev/null 2>&1 || true
$PHP_BIN artisan cache:clear >/dev/null 2>&1 || true
log_success "Cache Laravel : ${BOLD}Dibersihkan${NC}"

# ──────────────────────────────────────────────────────────────────────────────
# KONFIGURASI NGINX: Tulis file lengkap (BUKAN patch dengan sed)
# ──────────────────────────────────────────────────────────────────────────────
SITE_DOMAIN="$DOMAIN_NAME"
NGINX_VHOST="/www/server/panel/vhost/nginx/${SITE_DOMAIN}.conf"
NGINX_REWRITE="/www/server/panel/vhost/rewrite/${SITE_DOMAIN}.conf"
SSL_CERT_DIR="/www/server/panel/vhost/cert/${SITE_DOMAIN}"

log_info "Menulis konfigurasi Nginx (${BOLD}full-replace, bukan patch${NC})..."

# Tentukan enable-php file yang tersedia
PHP_ENABLE_CONF="enable-php-${PHP_MAJOR_MINOR}.conf"
if [ ! -f "/www/server/nginx/conf/${PHP_ENABLE_CONF}" ]; then
    for conf_ver in 84 83 82 81 80; do
        if [ -f "/www/server/nginx/conf/enable-php-${conf_ver}.conf" ]; then
            PHP_ENABLE_CONF="enable-php-${conf_ver}.conf"
            break
        fi
    done
fi

# Tentukan blok SSL (cek beberapa lokasi sertifikat aaPanel)
SSL_BLOCK=""
HTTPS_REDIRECT=""
SSL_CERT_FULLCHAIN=""
SSL_CERT_PRIVKEY=""

# Cek lokasi sertifikat: cert/ → letsencrypt/
for cert_dir in "${SSL_CERT_DIR}" "/www/server/panel/vhost/letsencrypt/${SITE_DOMAIN}"; do
    if [ -f "${cert_dir}/fullchain.pem" ] && [ -f "${cert_dir}/privkey.pem" ]; then
        SSL_CERT_FULLCHAIN="${cert_dir}/fullchain.pem"
        SSL_CERT_PRIVKEY="${cert_dir}/privkey.pem"
        break
    fi
done

if [ -n "$SSL_CERT_FULLCHAIN" ]; then
    SSL_BLOCK="    listen 443 ssl http2;
    ssl_certificate    ${SSL_CERT_FULLCHAIN};
    ssl_certificate_key    ${SSL_CERT_PRIVKEY};
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers EECDH+CHACHA20:EECDH+CHACHA20-draft:EECDH+AES128:RSA+AES128:EECDH+AES256:RSA+AES256:EECDH+3DES:RSA+3DES:!MD5;
    ssl_prefer_server_ciphers on;
    ssl_session_timeout 10m;
    ssl_session_cache shared:SSL:10m;"
    HTTPS_REDIRECT="
    # Force HTTPS redirect
    if (\$server_port !~ 443){
        rewrite ^(/.*)$ https://\$host\$1 permanent;
    }"
    # Update APP_URL ke https
    sed -i "s|APP_URL=http://${DOMAIN_NAME}|APP_URL=https://${DOMAIN_NAME}|g" .env 2>/dev/null
    log_info "SSL Certificate : ${BOLD}Terdeteksi, HTTPS + Auto-Redirect aktif${NC}"
else
    log_info "SSL Certificate : ${BOLD}Tidak ditemukan, hanya HTTP port 80${NC}"
    log_info "Tip: Aktifkan SSL via aaPanel → Website → ${SITE_DOMAIN} → SSL → Let's Encrypt"
fi

# Tulis Rewrite file Laravel
mkdir -p "$(dirname "$NGINX_REWRITE")" 2>/dev/null
cat > "$NGINX_REWRITE" << 'REWRITEEOF'
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
REWRITEEOF

# Tulis LENGKAP file vhost Nginx (tidak patch/sed)
cat > "$NGINX_VHOST" << NGINXEOF
server
{
    listen 80;
${SSL_BLOCK}
    server_name ${SITE_DOMAIN};
    index index.php index.html index.htm;
    root ${PROJECT_DIR}/public;
${HTTPS_REDIRECT}

    # PHP Handler (aaPanel managed)
    include ${PHP_ENABLE_CONF};

    # Laravel URL Rewrite
    include /www/server/panel/vhost/rewrite/${SITE_DOMAIN}.conf;

    # Forbidden files or directories
    location ~ ^/(\.user.ini|\.htaccess|\.git|\.env|\.svn|\.project|LICENSE|README.md)
    {
        return 404;
    }

    # Directory verification for SSL certificate
    location ~ \.well-known{
        allow all;
    }

    # Block sensitive files in well-known
    if ( \$uri ~ "^/\.well-known/.*\.(php|jsp|py|js|css|lua|ts|go|zip|tar\.gz|rar|7z|sql|bak)$" ) {
        return 403;
    }

    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$
    {
        expires 30d;
        error_log /dev/null;
        access_log /dev/null;
    }

    location ~ .*\.(js|css)?$
    {
        expires 12h;
        error_log /dev/null;
        access_log /dev/null;
    }

    access_log /www/wwwlogs/${SITE_DOMAIN}.log;
    error_log /www/wwwlogs/${SITE_DOMAIN}.error.log;
}
NGINXEOF

# Tes & Reload Nginx
if nginx -t 2>/dev/null; then
    nginx -s reload 2>/dev/null || systemctl reload nginx 2>/dev/null || true
    log_success "Nginx Vhost : ${BOLD}Ditulis lengkap & Reload berhasil${NC}"
    log_success "Nginx Root : ${BOLD}${PROJECT_DIR}/public${NC}"
    log_success "Nginx PHP : ${BOLD}${PHP_ENABLE_CONF}${NC}"
    log_success "Nginx Rewrite : ${BOLD}Laravel try_files aktif${NC}"
    STATUS[nginx]=0
else
    log_fail "Nginx config gagal validasi! Cek error:"
    nginx -t 2>&1
    STATUS[nginx]=1
fi

# Juga tangani Apache jika ada
APACHE_VHOST="/www/server/panel/vhost/apache/${SITE_DOMAIN}.conf"
if [ -f "$APACHE_VHOST" ]; then
    sed -i "s|DocumentRoot \"$PROJECT_DIR\"|DocumentRoot \"$PROJECT_DIR/public\"|g" "$APACHE_VHOST" 2>/dev/null
    sed -i "s|<Directory \"$PROJECT_DIR\">|<Directory \"$PROJECT_DIR/public\">|g" "$APACHE_VHOST" 2>/dev/null
    systemctl reload httpd 2>/dev/null || systemctl reload apache2 2>/dev/null || true
    log_success "Apache Config : ${BOLD}DocumentRoot /public Aktif${NC}"
fi

# Juga tangani OpenLiteSpeed jika ada
OLS_VHOST="/www/server/panel/vhost/openlitespeed/${SITE_DOMAIN}.conf"
if [ -f "$OLS_VHOST" ]; then
    sed -i "s|docRoot                   $PROJECT_DIR|docRoot                   $PROJECT_DIR/public|g" "$OLS_VHOST" 2>/dev/null
    /usr/local/lsws/bin/lswsctrl reload 2>/dev/null || true
    log_success "OpenLiteSpeed : ${BOLD}docRoot /public Aktif${NC}"
fi

# ==============================================================================
# RINGKASAN AKHIR & CHECKLIST STATUS
# ==============================================================================
log_header "📋 CHECKLIST STATUS INSTALASI JAGOAN"

print_status_line "php"         "PHP Version         " "PHP $PHP_VER_STR ($PHP_BIN)"
print_status_line "composer"    "Composer            " "${COMPOSER_VER_OUT:-N/A}"
print_status_line "git"         "Git Clone           " "origin/main → $PROJECT_DIR"
print_status_line "env"         "Konfigurasi .env    " "DB=$DB_NAME, URL=http://$DOMAIN_NAME"
print_status_line "vendor"      "Vendor Composer     " "vendor/autoload.php"
print_status_line "appkey"      "Laravel APP_KEY     " "Generated"
print_status_line "migrate"     "Migrasi Database    " "MySQL $DB_NAME"
print_status_line "permissions" "Permissions         " "www:www, chmod 777 storage"
print_status_line "nginx"       "Nginx Vhost         " "Root /public, PHP Handler, Laravel Rewrite"

# Hitung gagal
FAIL_COUNT=0
for key in php composer git env vendor appkey migrate permissions nginx; do
    [ "${STATUS[$key]}" -eq 1 ] && ((FAIL_COUNT++))
done

echo ""
if [ "$FAIL_COUNT" -eq 0 ]; then
    echo -e "${GREEN}${BOLD}🎉 SEMUA LANGKAH BERHASIL! Aplikasi siap diakses.${NC}"
    echo -e ""
    echo -e "  Buka di browser: ${BOLD}http://$DOMAIN_NAME${NC}"
    echo -e "  Untuk update  : ${BOLD}bash update-aapanel.sh${NC}"
else
    echo -e "${RED}${BOLD}⚠️  ADA $FAIL_COUNT LANGKAH GAGAL! Periksa item bertanda ✖ di atas.${NC}"
fi

echo ""
echo -e "${YELLOW}${BOLD}📌 Tips Tambahan aaPanel (Opsional):${NC}"
echo -e " 1. aaPanel → Website → ${BOLD}$DOMAIN_NAME${NC} → Site directory → Running directory: ${BOLD}/public${NC}"
echo -e " 2. aaPanel → Website → ${BOLD}$DOMAIN_NAME${NC} → Site directory → Hapus centang ${BOLD}open_basedir${NC}"
echo -e " 3. aaPanel → Website → ${BOLD}$DOMAIN_NAME${NC} → SSL → Apply Let's Encrypt"
echo ""
