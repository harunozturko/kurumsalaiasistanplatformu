# Kurumsal AI Asistan Platformu

Docker tabanli full-stack web uygulamasi:
- **Backend**: Symfony 7.x (PHP 8.3)
- **Frontend**: Angular 19+ (Node 22 LTS)
- **Veritabani**: PostgreSQL 16
- **Cache**: Redis 7
- **Web Server**: Nginx 1.27

## Servisler ve Portlar

| Servis        | URL                          | Aciklama                       |
|---------------|------------------------------|--------------------------------|
| Nginx (proxy) | http://localhost:8080        | Ana giris noktasi              |
| Angular       | http://localhost:4200        | Angular dev server (dogrudan)  |
| Symfony API   | http://localhost:8080/api    | Backend API (nginx uzerinden)  |
| Mailhog       | http://localhost:8025        | E-posta test UI                |
| pgAdmin       | http://localhost:5050        | PostgreSQL yonetim paneli      |
| PostgreSQL    | localhost:5432               | Veritabani                     |
| Redis         | localhost:6379               | Cache                          |

> Not: Nginx 80 yerine **8080** portunda calisir, cunku makinede port 80'i baska
> bir servis (orn. XAMPP/WAMP Apache) tutabiliyor. Port `.env` icindeki
> `HTTP_PORT` ile degistirilebilir.

## Hizli Baslangic

```bash
# 1. Repo'yu klonla ve env dosyasini hazirla
cp .env.example .env

# 2. Ilk kurulumu yap (image build + container baslat)
docker compose up -d --build

# 3. Loglari takip et (Symfony ve Angular kurulumunu izle)
docker compose logs -f
```

> Not: Ilk acilista Symfony ve Angular projeleri otomatik kurulur.
> Bu islem internet hizina bagli olarak 3-10 dakika surebilir.

## Saglik Kontrolu (Health Check)

Sistemin ayakta oldugunu dogrulamak icin:

```bash
curl http://localhost:8080/api/health
# {"status":"ok","service":"Kurumsal AI Asistan API","env":"dev","timestamp":"..."}
```

## Gelistirme Komutlari

```bash
make help              # Tum komutlari listele
make shell-php         # PHP container'ina baglan
make shell-node        # Angular container'ina baglan
make migrate           # Veritabani migration calistir
make cache-clear       # Symfony cache temizle
make logs              # Tum loglari izle
```

## Proje Yapisi

```
backend/           - Symfony 7.x (otomatik olusturulur)
frontend/          - Angular 19+ (otomatik olusturulur)
docker/
  php/             - PHP 8.3 FPM Dockerfile + config
  nginx/           - Nginx config
  node/            - Node 22 Dockerfile + entrypoint
  postgres/        - PostgreSQL init scriptleri
docker-compose.yml
docker-compose.prod.yml
.env               - Ortam degiskenleri (git'e eklenmez)
.env.example       - Ornek ortam degiskenleri
Makefile           - Gelistirici komutlari
```
