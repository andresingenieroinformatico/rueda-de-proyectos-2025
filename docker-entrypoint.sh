#!/bin/sh
set -e

# Si no viene PORT desde el entorno, fallback a 80 (opcional)
: "${PORT:=80}"

# Modificar configuración de Apache en tiempo de ejecución para escuchar en $PORT
# Intentamos reemplazar las ocurrencias más comunes. Reemplazamos cualquier 'Listen' y VirtualHost por seguridad.
echo "[INFO] Using PORT=${PORT}" >&2

# ports.conf: reemplazar cualquier línea Listen por la que provee la plataforma
if grep -qE "^\s*Listen\s+" /etc/apache2/ports.conf 2>/dev/null; then
	sed -ri "s/^\s*Listen\s+.*/Listen ${PORT}/" /etc/apache2/ports.conf || true
else
	echo "Listen ${PORT}" >> /etc/apache2/ports.conf
fi

# Actualizar VirtualHost en sites-available y sites-enabled para usar el PORT
for f in /etc/apache2/sites-available/*.conf /etc/apache2/sites-enabled/*.conf; do
	[ -e "$f" ] || continue
	sed -ri "s@<VirtualHost \*:[0-9]+>@<VirtualHost *:${PORT}>@g" "$f" || true
	sed -ri "s@:\\d+>@:${PORT}>@g" "$f" || true
done

# Asegurar ServerName global para evitar warnings y binding ambiguo
if ! grep -q "^ServerName" /etc/apache2/apache2.conf 2>/dev/null; then
	echo "ServerName localhost" >> /etc/apache2/apache2.conf || true
fi

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

# --- Debug: listar módulos y archivos relacionados para logs ---
echo "[DEBUG] /etc/apache2/mods-enabled:" >&2
ls -la /etc/apache2/mods-enabled 2>&1 | sed -n '1,200p' >&2 || true
echo "[DEBUG] Contenido de archivos mpm_*.load (si existen):" >&2
for f in /etc/apache2/mods-enabled/mpm_*.load; do
	[ -e "$f" ] || continue
	echo "--- $f ---" >&2
	sed -n '1,200p' "$f" >&2 || true
done
echo "[DEBUG] Buscando 'LoadModule .*mpm_' en /etc/apache2:" >&2
grep -R "LoadModule .*mpm_" /etc/apache2 2>&1 | sed -n '1,200p' >&2 || true
echo "[DEBUG] apachectl -M output (buscar mpm):" >&2
apachectl -M 2>&1 | sed -n '1,200p' >&2 || true

echo "[DEBUG] /etc/apache2/ports.conf:" >&2
sed -n '1,200p' /etc/apache2/ports.conf 2>&1 | sed -n '1,200p' >&2 || true
echo "[DEBUG] /etc/apache2/sites-enabled/000-default.conf:" >&2
sed -n '1,200p' /etc/apache2/sites-enabled/000-default.conf 2>&1 | sed -n '1,200p' >&2 || true

echo "[DEBUG] Sockets TCP escuchando (ss -ltnp):" >&2
ss -ltnp 2>&1 | sed -n '1,200p' >&2 || true
echo "[DEBUG] fallback netstat -tulpn:" >&2
netstat -tulpn 2>&1 | sed -n '1,200p' >&2 || true



# Ejecutar el comando por defecto (apache2-foreground)
exec "$@"
