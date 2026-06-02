#!/bin/bash
set -e

echo ""
echo "════════════════════════════════════════════════════════"
echo "  Kurumsal AI Asistan — PHP/Symfony Container"
echo "════════════════════════════════════════════════════════"

# ─── Symfony 7.x kurulumu (ilk boot) ──────────────────────────────────────
if [ ! -f "/var/www/html/composer.json" ]; then
    echo ""
    echo "==> İLK BOOT: Symfony 7.x projesi kuruluyor..."
    echo "    Bu işlem birkaç dakika sürebilir."
    echo ""

    # Geçici dizinde oluştur (mevcut .gitkeep'e takılmamak için)
    composer create-project symfony/skeleton:"^7.2" /tmp/symfony_project \
        --no-interaction \
        --prefer-dist

    # webapp paketi kur (Twig, Doctrine, Security, Form, Validator, vb.)
    cd /tmp/symfony_project
    composer require webapp --no-interaction

    # Dosyaları çalışma dizinine kopyala
    cp -a /tmp/symfony_project/. /var/www/html/
    rm -rf /tmp/symfony_project

    echo ""
    echo "==> Symfony projesi başarıyla kuruldu!"
fi

# ─── Composer bağımlılıkları ───────────────────────────────────────────────
if [ ! -d "/var/www/html/vendor" ]; then
    echo ""
    echo "==> Composer bağımlılıkları yükleniyor..."
    cd /var/www/html && composer install --no-interaction --prefer-dist
fi

# ─── Symfony var/ dizin izinleri ──────────────────────────────────────────
mkdir -p /var/www/html/var/cache /var/www/html/var/log
chmod -R 777 /var/www/html/var/ 2>/dev/null || true

# ─── Symfony cache temizle ────────────────────────────────────────────────
if [ -f "/var/www/html/bin/console" ]; then
    cd /var/www/html && php bin/console cache:warmup --no-interaction 2>/dev/null || true
fi

echo ""
echo "==> PHP-FPM başlatılıyor..."
echo ""

exec "$@"
