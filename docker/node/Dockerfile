FROM node:22-alpine

LABEL maintainer="Harun Ozturko"
LABEL description="Node 22 LTS — Angular 19+ Frontend Dev Server"

# ─── Angular CLI (son sürüm) ───────────────────────────────────────────────
RUN npm install -g @angular/cli@latest \
    && npm cache clean --force

WORKDIR /app

# ─── Entrypoint scripti ────────────────────────────────────────────────────
COPY docker/node/entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 4200

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["ng", "serve", "--host", "0.0.0.0", "--port", "4200", "--proxy-config", "proxy.conf.json"]
