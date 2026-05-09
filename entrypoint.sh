#!/bin/bash
set -e

# Iniciar PHP-FPM en background
php-fpm

# Iniciar Caddy en foreground
caddy run --config /workspace/Caddyfile