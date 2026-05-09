FROM caddy:latest as caddy
FROM php:8.2-fpm

# Copiar Caddy desde su imagen
COPY --from=caddy /usr/bin/caddy /usr/bin/caddy

# Instalar extensiones PHP que necesites
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copiar proyecto
COPY . /workspace
WORKDIR /workspace

# Exponer puerto
EXPOSE 8080

# Iniciar PHP-FPM y Caddy
CMD php-fpm -D && caddy run --config /workspace/Caddyfile