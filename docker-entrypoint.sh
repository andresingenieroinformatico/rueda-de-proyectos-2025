#!/bin/sh
set -e

# Si no viene PORT desde el entorno, fallback a 10000 (opcional)
: "${PORT:=10000}"

# Modificar configuración de Apache en tiempo de ejecución para escuchar en $PORT
# Intentamos reemplazar las ocurrencias más comunes
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf || true
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g" /etc/apache2/sites-available/000-default.conf || true
sed -i "s/:80>/:${PORT}>/g" /etc/apache2/sites-available/000-default.conf || true

# Ejecutar el comando por defecto (apache2-foreground)
exec "$@"
