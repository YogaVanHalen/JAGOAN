#!/bin/bash

# ==============================================================================
# 🚀 JAGOAN - Automated Update Script for aaPanel / VPS Linux
# ==============================================================================
# Versi : 2.0.0 (Production-Ready)
# Penulis: YogaVanHalen
# Catatan: Script ini untuk memperbarui aplikasi yang sudah terinstall.
#          Untuk instalasi pertama, gunakan install-aapanel.sh
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

# Status Tracking
declare -A STATUS
STATUS[git]=2
STATUS[composer]=2
STATUS[vendor]=2
STATUS[migrate]=2
STATUS[cache]=2
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
log_header "🚀 PROSES PEMBARUAN (UPDATE) APLIKASI JAGOAN v2.0"

# Deteksi lokasi project
if [ -n "${BASH_SOURCE[0]:-}" ] && [ "${BASH_SOURCE[0]}" != "-" ]; then
    SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" 2>/dev/null && pwd)"
else
    SCRIPT_DIR="$(pwd)"
fi
cd "$SCRIPT_DIR" || exit 1

CURRENT_PWD="$(pwd)"
if [ "$CURRENT_PWD" == "/www/wwwroot" ] || [ "$CURRENT_PWD" == "/www/wwwroot/" ]; then
    log_fail "Script update tidak boleh dijalankan langsung di /www/wwwroot!"
    echo -e " Silakan masuk ke folder domain Anda terlebih dahulu, contoh:"
    echo -e "   cd /www/wwwroot/sub.domain.com && bash update-aapanel.sh"
    exit 1
fi

PROJECT_DIR="$CURRENT_PWD"
SITE_DOMAIN="$(basename "$PROJECT_DIR")"
log_success "Lokasi Proyek : ${BOLD}$PROJECT_DIR${NC}"
log_success "Domain        : ${BOLD}$SITE_DOMAIN${NC}"

export COMPOSER_ALLOW_SUPERUSER=1
git config --global --add safe.directory "$PROJECT_DIR" 2>/dev/null
git config --global --add safe.directory "*" 2>/dev/null

# Hapus pembatasan aaPanel
chattr -i .user.ini public/.user.ini 2>/dev/null || true
rm -f .user.ini public/.user.ini 2>/dev/null
rm -f "/www/server/panel/vhost/open_basedir/nginx/${SITE_DOMAIN}.conf" 2>/dev/null
rm -f "/www/server/panel/vhost/open_basedir/apache/${SITE_DOMAIN}.conf" 2>/dev/null

# ==============================================================================
# LANGKAH 1: Pull Pembaruan Kode dari GitHub
# ==============================================================================
log_step "Langkah 1: Menarik Pembaruan Kode dari GitHub"

if git fetch origin main && git reset --hard origin/main; then
    LATEST_COMMIT=$(git log -1 --pretty=format:"%h - %s" 2>/dev/null || echo "N/A")
    log_success "Git Reset : ${BOLD}Kode diperbarui ke versi terbaru${NC}"
    log_info "Commit    : ${BOLD}$LATEST_COMMIT${NC}"
    STATUS[git]=0
else
    log_fail "Gagal melakukan git fetch/reset dari GitHub!"
    STATUS[git]=1
fi

# Pastikan .env Turnstile keys ada
if [ -f .env ]; then
    if ! grep -q "TURNSTILE_SITE_KEY" .env; then
        echo "" >> .env
        echo "# Cloudflare Turnstile Keys" >> .env
        echo "TURNSTILE_SITE_KEY=" >> .env
        echo "TURNSTILE_SECRET_KEY=" >> .env
    fi
fi

# ==============================================================================
# LANGKAH 2: Deteksi PHP 8.2+ & Composer 2.x
# ==============================================================================
log_step "Langkah 2: Pemeriksaan PHP & Composer"

PHP_BIN="php"
BEST_PHP_VER=0

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
    log_fail "PHP 8.2+ tidak ditemukan!"
    exit 1
fi

PHP_VER_STR=$($PHP_BIN -r "echo PHP_VERSION;" 2>/dev/null)
PHP_MAJOR_MINOR=$($PHP_BIN -r "echo PHP_MAJOR_VERSION . PHP_MINOR_VERSION;" 2>/dev/null || echo "84")
log_success "PHP Executable : ${BOLD}PHP $PHP_VER_STR${NC} ($PHP_BIN)"

# Siapkan Composer 2.x
COMPOSER_CMD=""

NEED_DOWNLOAD=0
if [ ! -f "composer.phar" ]; then
    NEED_DOWNLOAD=1
else
    COMPOSER_MINOR=$($PHP_BIN composer.phar --version 2>/dev/null | grep -oE '2\.([0-9]+)' | head -1 | cut -d. -f2 || echo "0")
    if [ "${COMPOSER_MINOR:-0}" -lt 2 ]; then
        NEED_DOWNLOAD=1
    fi
fi

if [ "$NEED_DOWNLOAD" -eq 1 ]; then
    log_info "Mengunduh composer.phar versi terbaru..."
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
    log_success "Composer Ready : ${BOLD}$COMPOSER_VER_OUT${NC}"
    STATUS[composer]=0
else
    log_fail "Composer tidak tersedia!"
    STATUS[composer]=1
    exit 1
fi

# ==============================================================================
# LANGKAH 3: Update Dependensi Composer
# ==============================================================================
log_step "Langkah 3: Meng-update Paket Composer (Vendor)"

$COMPOSER_CMD install --no-interaction --prefer-dist --optimize-autoloader 2>&1 | tail -n 5 || \
$COMPOSER_CMD install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs 2>&1 | tail -n 5

if [ -f "vendor/autoload.php" ]; then
    log_success "Vendor Composer : ${BOLD}Sukses (vendor/autoload.php terverifikasi)${NC}"
    STATUS[vendor]=0
else
    log_fail "Vendor composer gagal diperbarui!"
    STATUS[vendor]=1
fi

# ==============================================================================
# LANGKAH 4: Migrasi Database & Perizinan Folder
# ==============================================================================
log_step "Langkah 4: Migrasi Database & Perizinan Folder"

# Pastikan direktori storage lengkap
mkdir -p storage/framework/views storage/framework/sessions storage/framework/cache storage/logs

if $PHP_BIN artisan migrate --force 2>&1 | tail -n 3; then
    log_success "Migrasi Database : ${BOLD}Berhasil Dijalankan${NC}"
    STATUS[migrate]=0
else
    log_fail "Gagal menjalankan migrasi database!"
    STATUS[migrate]=1
fi

chmod -R 777 storage bootstrap/cache database 2>/dev/null
chown -R www:www "$PROJECT_DIR" 2>/dev/null || chown -R www-data:www-data "$PROJECT_DIR" 2>/dev/null
log_success "Permissions : ${BOLD}Updated (chmod 777 storage & cache)${NC}"

# ==============================================================================
# LANGKAH 5: Refresh Cache Laravel & Konfigurasi Nginx
# ==============================================================================
log_step "Langkah 5: Refresh Cache & Konfigurasi Web Server"

$PHP_BIN artisan optimize:clear >/dev/null 2>&1 || true
$PHP_BIN artisan config:clear >/dev/null 2>&1 || true
$PHP_BIN artisan route:clear >/dev/null 2>&1 || true
$PHP_BIN artisan view:clear >/dev/null 2>&1 || true
$PHP_BIN artisan cache:clear >/dev/null 2>&1 || true
log_success "Cache Laravel : ${BOLD}Dibersihkan${NC}"
STATUS[cache]=0

# Hapus ulang .user.ini (aaPanel bisa re-create saat reload)
chattr -i .user.ini public/.user.ini 2>/dev/null || true
rm -f .user.ini public/.user.ini 2>/dev/null

# ──────────────────────────────────────────────────────────────────────────────
# KONFIGURASI NGINX: Tulis file lengkap (BUKAN patch dengan sed)
# ──────────────────────────────────────────────────────────────────────────────
NGINX_VHOST="/www/server/panel/vhost/nginx/${SITE_DOMAIN}.conf"
NGINX_REWRITE="/www/server/panel/vhost/rewrite/${SITE_DOMAIN}.conf"
SSL_CERT_DIR="/www/server/panel/vhost/cert/${SITE_DOMAIN}"

if [ -f "$NGINX_VHOST" ] || [ -d "/www/server/panel/vhost/nginx" ]; then
    log_info "Menulis konfigurasi Nginx lengkap..."

    # Tentukan enable-php file
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
        sed -i "s|APP_URL=http://${SITE_DOMAIN}|APP_URL=https://${SITE_DOMAIN}|g" .env 2>/dev/null
        log_info "SSL Certificate : ${BOLD}Terdeteksi, HTTPS + Auto-Redirect aktif${NC}"
    fi

    # Tulis Rewrite file Laravel
    mkdir -p "$(dirname "$NGINX_REWRITE")" 2>/dev/null
    cat > "$NGINX_REWRITE" << 'REWRITEEOF'
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
REWRITEEOF

    # Tulis LENGKAP file vhost Nginx
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
        log_success "Nginx Vhost : ${BOLD}Root /public, PHP Handler & Laravel Rewrite Aktif${NC}"
        STATUS[nginx]=0
    else
        log_fail "Nginx config gagal validasi!"
        nginx -t 2>&1
        STATUS[nginx]=1
    fi
fi

# Reload PHP-FPM
for fpm_service in "php-fpm-${PHP_MAJOR_MINOR}" "php-fpm-84" "php-fpm-83" "php-fpm-82" "php-fpm" "php${PHP_MAJOR_MINOR}-fpm"; do
    if systemctl reload "$fpm_service" 2>/dev/null; then
        log_success "PHP-FPM : ${BOLD}$fpm_service Reload Berhasil${NC}"
        break
    fi
done

# ==============================================================================
# RINGKASAN AKHIR
# ==============================================================================
log_header "📋 CHECKLIST STATUS UPDATE JAGOAN"

print_status_line "git"      "Git Pull         " "origin/main → $PROJECT_DIR"
print_status_line "composer" "Composer         " "${COMPOSER_VER_OUT:-N/A}"
print_status_line "vendor"   "Vendor Update    " "vendor/autoload.php"
print_status_line "migrate"  "Migrasi Database " "artisan migrate --force"
print_status_line "cache"    "Cache Clear      " "config, route, view, cache"
print_status_line "nginx"    "Nginx Vhost      " "Root /public, PHP Handler, Rewrite"

# Hitung gagal
FAIL_COUNT=0
for key in git composer vendor migrate cache nginx; do
    [ "${STATUS[$key]}" -eq 1 ] && ((FAIL_COUNT++))
done

echo ""
if [ "$FAIL_COUNT" -eq 0 ]; then
    echo -e "${GREEN}${BOLD}🎉 UPDATE BERHASIL! Aplikasi telah diperbarui ke versi terbaru.${NC}"
    echo -e "  Buka di browser: ${BOLD}http://$SITE_DOMAIN${NC}"
else
    echo -e "${RED}${BOLD}⚠️  ADA $FAIL_COUNT LANGKAH GAGAL! Periksa item bertanda ✖ di atas.${NC}"
fi
echo ""
