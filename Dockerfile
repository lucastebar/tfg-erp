FROM php:8.2-fpm

RUN apt-get update && apt-get install -y caddy && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY . /workspace
WORKDIR /workspace

EXPOSE 80

CMD caddy run --config /workspace/Caddyfile