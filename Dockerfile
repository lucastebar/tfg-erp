FROM php:8.2-fpm

RUN apt-get update && apt-get install -y caddy && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY . /workspace
WORKDIR /workspace

EXPOSE 8080

CMD sh -c "php-fpm -D && caddy run --config /workspace/Caddyfile"