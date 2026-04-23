#!/bin/sh
set -e

# Si no viene PORT desde el entorno, fallback a 10000 (opcional)
: "${PORT:=10000}"

# Modificar configuración de Apache en tiempo de ejecución para escuchar en $PORT
# Intentamos reemplazar las ocurrencias más comunes
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf || true
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g" /etc/apache2/sites-available/000-default.conf || true
sed -i "s/:80>/:${PORT}>/g" /etc/apache2/sites-available/000-default.conf || true

# Asegurar un único MPM en tiempo de arranque (previene "More than one MPM loaded").
# Preferimos `mpm_prefork` para compatibilidad con mod_php.
set +e
# Intentar deshabilitar MPMs alternativos y habilitar prefork
a2dismod mpm_event mpm_worker 2>/dev/null
a2enmod mpm_prefork 2>/dev/null

# Como protección extra, eliminar cualquier LoadModule mpm_* duplicado en mods-enabled
for f in /etc/apache2/mods-enabled/mpm_*.load; do
	[ -e "$f" ] || continue
	if [ "$(basename "$f")" != "mpm_prefork.load" ]; then
		rm -f "$f"
	fi
done
set -e

# Ejecutar el comando por defecto (apache2-foreground)
exec "$@"
