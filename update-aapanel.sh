#!/bin/bash

# ==============================================================================
# 🚀 JAGOAN - Automated Update Script for aaPanel / VPS Linux
# ==============================================================================

# Warna & Format Terminal
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m'

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

clear
log_header "🚀 PROSES PEMBARUAN (UPDATE) APLIKASI JAGOAN"

# Dapatkan lokasi script saat ini
if [ -n "${BASH_SOURCE[0]}" ] && [ "${BASH_SOURCE[0]}" != "-" ]; then
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

export COMPOSER_ALLOW_SUPERUSER=1
git config --global --add safe.directory "$PROJECT_DIR" 2>/dev/null
git config --global --add safe.directory "*" 2>/dev/null

# Hapus pembatasan .user.ini & open_basedir aaPanel jika ada
chattr -i .user.ini public/.user.ini 2>/dev/null
rm -f .user.ini public/.user.ini /www/server/panel/vhost/open_basedir/nginx/${SITE_DOMAIN}.conf /www/server/panel/vhost/open_basedir/apache/${SITE_DOMAIN}.conf 2>/dev/null

# ------------------------------------------------------------------------------
# 1. Pull Pembaruan Kode Terbaru dari GitHub
# ------------------------------------------------------------------------------
log_step "Langkah 1: Menarik Pembaruan Kode dari GitHub"
git fetch origin main
git reset --hard origin/main
if [ $? -eq 0 ]; then
    log_success "Git Reset : ${BOLD}Kode berhasil diperbarui ke versi terbaru (main)${NC}"
else
    log_fail "Gagal melakukan git fetch/reset dari GitHub!"
fi

if [ -f .env ]; then
    if ! grep -q "TURNSTILE_SITE_KEY" .env; then
        echo "" >> .env
        echo "# Cloudflare Turnstile Keys" >> .env
        echo "TURNSTILE_SITE_KEY=" >> .env
        echo "TURNSTILE_SECRET_KEY=" >> .env
    fi
fi

# ------------------------------------------------------------------------------
# 2. Deteksi PHP & Composer Runtime API
# ------------------------------------------------------------------------------
log_step "Langkah 2: Pemeriksaan PHP Executable & Composer API"

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

PHP_VER_STR=$($PHP_BIN -r "echo PHP_VERSION;" 2>/dev/null)
log_success "PHP Executable : ${BOLD}PHP $PHP_VER_STR${NC} ($PHP_BIN)"

COMPOSER_CMD=""
export COMPOSER_ALLOW_SUPERUSER=1

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

# ------------------------------------------------------------------------------
# 3. Update Dependensi Composer & Migrasi Database
# ------------------------------------------------------------------------------
log_step "Langkah 3: Meng-update Paket Composer (Vendor)"

$COMPOSER_CMD install --no-interaction --prefer-dist --optimize-autoloader || $COMPOSER_CMD install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs
if [ -f "vendor/autoload.php" ]; then
    log_success "Update Vendor Composer : ${BOLD}Sukses (vendor/autoload.php terverifikasi)${NC}"
else
    log_fail "Vendor composer gagal diperbarui!"
fi

log_step "Langkah 4: Migrasi Database & Perizinan Folder"

$PHP_BIN artisan migrate --force
if [ $? -eq 0 ]; then
    log_success "Migrasi Database : ${BOLD}Berhasil Dijalankan${NC}"
else
    log_fail "Gagal menjalankan migrasi database!"
fi

chmod -R 777 storage bootstrap/cache database 2>/dev/null
chown -R www:www "$PROJECT_DIR" 2>/dev/null || chown -R www-data:www-data "$PROJECT_DIR" 2>/dev/null
log_success "Permissions Folder : ${BOLD}Updated (chmod 777 storage & cache)${NC}"

# ------------------------------------------------------------------------------
# 4. Refresh Cache & Reload OPcache
# ------------------------------------------------------------------------------
log_step "Langkah 5: Refresh Cache Laravel & Reload OPcache"

$PHP_BIN artisan optimize:clear >/dev/null
$PHP_BIN artisan view:clear >/dev/null
$PHP_BIN artisan route:clear >/dev/null
$PHP_BIN artisan config:clear >/dev/null
$PHP_BIN artisan cache:clear >/dev/null
log_success "Cache Laravel : ${BOLD}Dibersihkan${NC}"

systemctl reload php-fpm-84 2>/dev/null || systemctl reload php-fpm-83 2>/dev/null || systemctl reload php-fpm-82 2>/dev/null || service php-fpm reload 2>/dev/null || true
log_success "PHP-FPM Reload : ${BOLD}Reset OPcache Berhasil${NC}"

log_header "🎉 PROSES UPDATE JAGOAN BERHASIL DISLESAIKAN!"
echo -e "${GREEN}✔ [STATUS OK] Aplikasi Anda telah diperbarui ke versi terbaru.${NC}\n"
