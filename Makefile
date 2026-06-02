.PHONY: help build up down restart logs ps clean install \
        shell-php shell-node shell-postgres \
        migrate migrate-diff migrate-rollback migrate-status \
        cache-clear fixtures \
        test test-backend test-coverage \
        npm-install npm-build npm-test \
        composer-install composer-update composer-require \
        db-create db-drop db-reset

DOCKER_COMPOSE = docker compose
PHP_EXEC       = $(DOCKER_COMPOSE) exec php
NODE_EXEC      = $(DOCKER_COMPOSE) exec angular

## ─── Yardım ────────────────────────────────────────────────────────────────
help: ## Bu yardım mesajını göster
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | \
		awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-25s\033[0m %s\n", $$1, $$2}'

## ─── Docker ────────────────────────────────────────────────────────────────
build: ## Docker image'larını oluştur
	$(DOCKER_COMPOSE) build --no-cache

up: ## Tüm container'ları başlat
	$(DOCKER_COMPOSE) up -d

down: ## Tüm container'ları durdur
	$(DOCKER_COMPOSE) down

restart: ## Tüm container'ları yeniden başlat
	$(DOCKER_COMPOSE) restart

logs: ## Tüm logları takip et
	$(DOCKER_COMPOSE) logs -f

logs-php: ## PHP loglarını takip et
	$(DOCKER_COMPOSE) logs -f php

logs-nginx: ## Nginx loglarını takip et
	$(DOCKER_COMPOSE) logs -f nginx

logs-angular: ## Angular loglarını takip et
	$(DOCKER_COMPOSE) logs -f angular

ps: ## Container durumlarını göster
	$(DOCKER_COMPOSE) ps

clean: ## Container, volume ve image'ları temizle (DİKKAT: veri silinir!)
	$(DOCKER_COMPOSE) down -v --rmi local --remove-orphans

## ─── İlk Kurulum ───────────────────────────────────────────────────────────
install: ## İlk kurulum — image build + container'ları başlat
	@echo "==> .env dosyası kontrol ediliyor..."
	@test -f .env || cp .env.example .env
	@echo "==> Docker image'ları oluşturuluyor..."
	$(DOCKER_COMPOSE) build
	@echo "==> Container'lar başlatılıyor (ilk açılışta Symfony ve Angular kurulumu yapılır)..."
	$(DOCKER_COMPOSE) up -d
	@echo ""
	@echo "==> İlk boot Symfony ve Angular projelerini otomatik kuruyor."
	@echo "    İlerlemeyi takip etmek için: make logs"
	@echo ""
	@echo "==> Servis adresleri:"
	@echo "    Frontend (Angular)   : http://localhost:4200"
	@echo "    Backend  (Symfony)   : http://localhost/api"
	@echo "    Nginx (proxy)        : http://localhost"
	@echo "    Mailhog              : http://localhost:8025"
	@echo "    pgAdmin              : http://localhost:5050"

## ─── Shell ─────────────────────────────────────────────────────────────────
shell-php: ## PHP container'ına bash ile bağlan
	$(DOCKER_COMPOSE) exec php bash

shell-node: ## Angular/Node container'ına sh ile bağlan
	$(DOCKER_COMPOSE) exec angular sh

shell-postgres: ## PostgreSQL container'ına psql ile bağlan
	$(DOCKER_COMPOSE) exec postgres psql -U app -d app

shell-redis: ## Redis container'ına redis-cli ile bağlan
	$(DOCKER_COMPOSE) exec redis redis-cli -a secret

## ─── Symfony / Veritabanı ──────────────────────────────────────────────────
migrate: ## Bekleyen migration'ları çalıştır
	$(PHP_EXEC) php bin/console doctrine:migrations:migrate --no-interaction

migrate-diff: ## Değişikliklerden migration dosyası oluştur
	$(PHP_EXEC) php bin/console doctrine:migrations:diff

migrate-rollback: ## Son migration'ı geri al
	$(PHP_EXEC) php bin/console doctrine:migrations:execute --down $(version)

migrate-status: ## Migration durumunu göster
	$(PHP_EXEC) php bin/console doctrine:migrations:status

db-create: ## Veritabanını oluştur
	$(PHP_EXEC) php bin/console doctrine:database:create --if-not-exists

db-drop: ## Veritabanını sil
	$(PHP_EXEC) php bin/console doctrine:database:drop --force

db-reset: ## Veritabanını sıfırla (sil + oluştur + migrate)
	$(PHP_EXEC) php bin/console doctrine:database:drop --force --if-exists
	$(PHP_EXEC) php bin/console doctrine:database:create
	$(PHP_EXEC) php bin/console doctrine:migrations:migrate --no-interaction

fixtures: ## Test verisi yükle
	$(PHP_EXEC) php bin/console doctrine:fixtures:load --no-interaction

cache-clear: ## Symfony önbelleğini temizle
	$(PHP_EXEC) php bin/console cache:clear

cache-warmup: ## Symfony önbelleğini ısıt
	$(PHP_EXEC) php bin/console cache:warmup

## ─── PHP / Composer ────────────────────────────────────────────────────────
composer-install: ## Composer bağımlılıklarını yükle
	$(PHP_EXEC) composer install --no-interaction

composer-update: ## Composer bağımlılıklarını güncelle
	$(PHP_EXEC) composer update --no-interaction

composer-require: ## Paket ekle — kullanım: make composer-require pkg=vendor/package
	$(PHP_EXEC) composer require $(pkg)

## ─── Testler ───────────────────────────────────────────────────────────────
test: test-backend ## Tüm testleri çalıştır

test-backend: ## Symfony/PHPUnit testlerini çalıştır
	$(PHP_EXEC) php bin/phpunit

test-coverage: ## Test coverage raporu oluştur
	$(PHP_EXEC) php bin/phpunit --coverage-html var/coverage

## ─── Angular / NPM ─────────────────────────────────────────────────────────
npm-install: ## npm paketlerini yükle
	$(NODE_EXEC) npm install

npm-update: ## npm paketlerini güncelle
	$(NODE_EXEC) npm update

npm-build: ## Angular uygulamasını production için derle
	$(NODE_EXEC) npm run build

npm-test: ## Angular testlerini çalıştır
	$(NODE_EXEC) npm test -- --watch=false --browsers=ChromeHeadless

ng: ## Angular CLI komutu — kullanım: make ng cmd="generate component my-comp"
	$(NODE_EXEC) ng $(cmd)
