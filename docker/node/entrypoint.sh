#!/bin/sh
set -e

echo ""
echo "════════════════════════════════════════════════════════"
echo "  Kurumsal AI Asistan — Node/Angular Container"
echo "════════════════════════════════════════════════════════"

# ─── Angular projesi kurulumu (ilk boot) ──────────────────────────────────
if [ ! -f "/app/package.json" ]; then
    echo ""
    echo "==> İLK BOOT: Angular projesi kuruluyor..."
    echo "    Bu işlem birkaç dakika sürebilir."
    echo ""

    # Geçici dizinde proje oluştur
    cd /tmp
    ng new kurumsal-frontend \
        --directory=kurumsal-frontend \
        --routing=true \
        --style=scss \
        --standalone \
        --skip-git=true \
        --no-interactive \
        --defaults

    # Dosyaları çalışma dizinine kopyala
    cp -a /tmp/kurumsal-frontend/. /app/
    rm -rf /tmp/kurumsal-frontend

    echo ""
    echo "==> Angular projesi başarıyla kuruldu!"
fi

# ─── npm bağımlılıkları ────────────────────────────────────────────────────
if [ ! -d "/app/node_modules" ] || [ ! -f "/app/node_modules/.bin/ng" ]; then
    echo ""
    echo "==> npm bağımlılıkları yükleniyor..."
    cd /app && npm install
fi

# ─── API proxy konfigürasyonu ─────────────────────────────────────────────
if [ ! -f "/app/proxy.conf.json" ]; then
    echo "==> Nginx proxy konfigürasyonu oluşturuluyor..."
    cat > /app/proxy.conf.json << 'PROXY_EOF'
{
  "/api": {
    "target": "http://nginx",
    "secure": false,
    "changeOrigin": true,
    "logLevel": "debug"
  }
}
PROXY_EOF
fi

echo ""
echo "==> Angular dev server başlatılıyor (http://localhost:4200)..."
echo ""

exec "$@"
