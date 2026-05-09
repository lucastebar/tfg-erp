FROM caddy:latest as caddy
FROM php:8.2-fpm

COPY --from=caddy /usr/bin/caddy /usr/bin/caddy

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY . /workspace
WORKDIR /workspace

RUN chmod +x /workspace/entrypoint.sh

EXPOSE 8080

CMD ["/workspace/entrypoint.sh"]