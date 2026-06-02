FROM php:8.3-fpm-alpine

LABEL maintainer="Harun Ozturko"
LABEL description="PHP 8.3 FPM — Symfony 7 Backend"

# ─── Sistem bağımlılıkları ─────────────────────────────────────────────────
RUN apk add --no-cache \
    bash \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    libzip-dev \
    icu-dev \
    icu-libs \
    postgresql-dev \
    oniguruma-dev \
    linux-headers \
    $PHPIZE_DEPS

# ─── PHP extension'ları ────────────────────────────────────────────────────
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        intl \
        zip \
        gd \
        exif \
        bcmath \
        opcache \
        sockets \
        mbstring \
        xml \
        pcntl \
        simplexml

# ─── PECL extension'ları ───────────────────────────────────────────────────
RUN pecl install \
        redis \
        apcu \
    && docker-php-ext-enable \
        redis \
        apcu \
    && rm -rf /tmp/pear

# ─── Composer (son sürüm) ──────────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ─── Çalışma dizini ───────────────────────────────────────────────────────
WORKDIR /var/www/html

# ─── Entrypoint scripti ────────────────────────────────────────────────────
COPY docker/php/entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm"]
