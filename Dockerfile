FROM caddy:latest as caddy
FROM php:8.2-fpm

COPY --from=caddy /usr/bin/caddy /usr/bin/caddy

RUN apt-get update && apt-get install -y supervisor && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY . /workspace
WORKDIR /workspace

# Crear directorio para supervisor
RUN mkdir -p /var/log/supervisor

# Crear config de supervisor
RUN cat > /etc/supervisor/conf.d/app.conf << 'EOF'
[program:php-fpm]
command=php-fpm
autostart=true
autorestart=true
stderr_logfile=/var/log/supervisor/php-fpm.err.log
stdout_logfile=/var/log/supervisor/php-fpm.out.log

[program:caddy]
command=caddy run --config /workspace/Caddyfile
autostart=true
autorestart=true
stderr_logfile=/var/log/supervisor/caddy.err.log
stdout_logfile=/var/log/supervisor/caddy.out.log
EOF

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]