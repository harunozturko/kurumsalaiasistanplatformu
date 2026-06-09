# Ubuntu (VirtualBox) Geliştirme Ortamı Kurulumu

Bu kılavuz, projeyi **VirtualBox üzerindeki Ubuntu Desktop** içinde sıfırdan ayağa kaldırmak içindir.
Amaç: Windows'un yavaş dosya sistemi köprüsünden kurtulmak — proje Ubuntu'nun kendi ext4 diskinde
durunca Docker bind mount'ları native hızda çalışır (istekler 6-9 sn yerine ms seviyesine iner).

> **Önemli:** Kaynak kod GitHub'dan `git clone` ile gelir. `vendor/`, `node_modules/`, `var/` ve
> veritabanı **taşınmaz** — bunları Docker, ilk açılışta Ubuntu içinde yeniden üretir. Yani
> "eksiksiz" derken tüm kaynak kodu kastediyoruz; gerisi otomatik.

---

## A. VirtualBox VM Ayarları

VM **kapalıyken** (Settings):

| Ayar | Öneri |
|------|-------|
| RAM (System → Motherboard) | En az **6–8 GB** (host'unda 16GB varsa) |
| CPU (System → Processor) | En az **4** çekirdek |
| Disk | En az **50 GB** (dynamically allocated) |

**Guest Additions kur** (pano paylaşımı, tam ekran çözünürlük, paylaşımlı klasör için):
VM açıkken menüden **Devices → Insert Guest Additions CD image…** → açılan CD'deki kurulumu çalıştır
→ yeniden başlat.

> Not: Docker container'ları VM'in Linux çekirdeğini **doğrudan** kullanır (container = VM değil),
> bu yüzden "Nested VT-x/AMD-V" ayarına **gerek yoktur**.
>
> (İleri/opsiyonel) Windows'ta Hyper-V/WSL açık olduğu için VirtualBox biraz yavaş çalışabilir.
> Şimdilik dokunma; kapatırsan Windows tarafındaki Docker'ın bozulur.

---

## B. Temel Araçlar

Ubuntu içinde bir **Terminal** aç ve:

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y git make curl ca-certificates
```

Git kimliğini ayarla:

```bash
git config --global user.name "Harun Ozturko"
git config --global user.email "harunozturk.work@gmail.com"
```

---

## C. Docker Engine + Compose Kurulumu

Linux'ta **Docker Desktop değil, Docker Engine** kuruyoruz. Sebep: Docker Desktop, Linux'ta kendi
sanal makinesini açar → VirtualBox içinde "VM içinde VM" olur (ağır ve sorunlu). Engine doğrudan
VM çekirdeğini kullanır, hafif ve standarttır.

```bash
# 1) Eski/çakışan sürümleri kaldır (varsa)
sudo apt remove -y docker docker-engine docker.io containerd runc 2>/dev/null

# 2) Docker'ın resmi GPG anahtarını ekle
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
sudo chmod a+r /etc/apt/keyrings/docker.gpg

# 3) Docker apt deposunu ekle
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo $VERSION_CODENAME) stable" | sudo tee /etc/apt/sources.list.d/docker.list

# 4) Kur
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# 5) sudo'suz kullanım için kullanıcını docker grubuna ekle
sudo usermod -aG docker $USER
```

> **5. adımdan sonra oturumu kapatıp tekrar aç** (veya `newgrp docker`), yoksa "permission denied"
> hatası alırsın.

Doğrula:

```bash
docker run hello-world      # "Hello from Docker!" görmelisin
docker compose version      # Docker Compose v2.x
```

---

## D. Projeyi Klonla

```bash
mkdir -p ~/projects && cd ~/projects
git clone https://github.com/harunozturko/kurumsalaiasistanplatformu.git
cd kurumsalaiasistanplatformu
cp .env.example .env
```

> `.env` dosyası git'e dâhil değildir (içinde gizli bilgiler olur), bu yüzden `.env.example`'dan
> kopyalıyoruz. İçindeki dev varsayılanları (DB parolası, JWT_SECRET) bu proje için yeterlidir.

---

## E. Çalıştır

```bash
# İlk açılış: Symfony ve Angular bağımlılıkları container içinde kurulur (internet hızına göre birkaç dk)
docker compose up -d --build

# İlerlemeyi izle (kurulum bitince Ctrl+C ile çık)
docker compose logs -f

# Veritabanı şemasını oluştur (DB sıfırdan başlar)
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
# Alternatif: make migrate
```

**Test et** (Ubuntu içindeki Firefox ile):

- Backend sağlık: <http://localhost:8080/api/health> → `{"status":"ok",...}`
- Frontend: <http://localhost:4200> → Register → Login → Dashboard

> Veritabanı boş başlar (eski Windows verisi taşınmaz), bu yüzden **register**'dan yeni bir kullanıcı
> oluşturup öyle giriş yap.

---

## F. (Opsiyonel) Ubuntu'ya Özel İyileştirmeler

**1) Docker performans volume'larını geri al.**
Windows'ta `vendor` ve `var` klasörlerini hızlandırmak için `docker-compose.yml`'de named volume
kullanmıştık. Ubuntu'nun ext4 diskinde buna **gerek yok**. O iki satırı kaldırırsan `vendor/` ve
`var/` proje klasöründe görünür olur → IDE otomatik tamamlaması düzelir, performans kaybı olmaz.

`docker-compose.yml` içinde `php` servisinden şu iki satırı sil:
```yaml
      - vendor_data:/var/www/html/vendor
      - var_data:/var/www/html/var
```
ve dosya sonundaki `volumes:` bloğundan `vendor_data:` ile `var_data:` satırlarını çıkar. Ardından:
```bash
docker compose up -d
```

**2) VS Code kur** (geliştirme için):
```bash
sudo snap install code --classic
```

---

## Hızlı Sorun Giderme

| Belirti | Çözüm |
|--------|-------|
| `permission denied ... docker.sock` | `usermod -aG docker $USER` sonrası **oturumu kapat/aç** |
| `port is already allocated` | O portu kullanan servisi durdur veya `.env`'de portu değiştir |
| İlk açılış çok uzun | Normal; `docker compose logs -f php` ve `... angular` ile izle |
| `migrations` tablo zaten var | DB zaten kuruluysa migration'ı atla; `make migrate-status` ile kontrol et |

---

## Faydalı Komutlar (Makefile)

```bash
make up            # container'ları başlat
make down          # durdur
make logs          # tüm loglar
make shell-php     # PHP container'ına gir
make migrate       # migration çalıştır
make ps            # durum
```
