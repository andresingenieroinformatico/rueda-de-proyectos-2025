# Despliegue en Railway (Guía rápida)

Esta guía explica cómo desplegar la aplicación en Railway usando MySQL (recomendado para el esquema incluido).

## Requisitos
- Cuenta en Railway (https://railway.app)
- Repo en GitHub con este proyecto
- MySQL o MariaDB (Railway plugin o servicio gestionado)

## Archivos relevantes
- Esquema SQL: [db/schema.sql](db/schema.sql)
- Conexión PDO/MySQL: [config/database/mysql_conexion.php](config/database/mysql_conexion.php)
- Configuración general: [config/config.php](config/config.php)
- Punto de entrada web: [public/index.php](public/index.php)
- Dockerfile: [Dockerfile](Dockerfile) (opcional)

---

## Variables de entorno necesarias (Railway → Project → Variables)
- DB_HOST — host de la BD (ej. provided by Railway)
- DB_PORT — puerto (ej. 3306)
- DB_NAME — nombre de la BD (ej. rueda_proyectos)
- DB_USER — usuario
- DB_PASS — contraseña
- DB_CHARSET — utf8mb4 (opcional)
- DEBUG — false (o true para debugging)
- BASE_URL — (opcional) `https://<tu-servicio>.railway.app`

Si mantienes Supabase opcionalmente:
- SUPABASE_URL
- SUPABASE_KEY
- SUPABASE_SERVICE_KEY

---

## Pasos de despliegue (forma recomendada)
1. Empuja el repositorio a GitHub.
2. En Railway crea un nuevo proyecto → Deploy from GitHub → selecciona el repo.
3. Añade el plugin MySQL (Add Plugin → MySQL). Copia las credenciales.
4. En Railway → Project → Variables, añade las variables de entorno listadas arriba usando las credenciales del plugin.

### Importar esquema de la BD
Puedes importar `db/schema.sql` de dos formas:

- Opción A (desde tu máquina):
```bash
mysql -h <DB_HOST> -P <DB_PORT> -u <DB_USER> -p'<DB_PASS>' <DB_NAME> < db/schema.sql
```
- Opción B (desde Railway): abre el SQL Console o Query editor que ofrece Railway y pega el contenido de [db/schema.sql](db/schema.sql), luego ejecuta.

> Nota: el archivo `db/schema.sql` ya está adaptado para MySQL y define `datos_proyectos` y `datos_ponentes`.

### Configurar Start Command
Si no quieres usar Docker (Railway soporta ambos):
- Start Command:
```bash
php -S 0.0.0.0:$PORT -t public
```
Railway expone la variable `$PORT` automáticamente.

Si prefieres desplegar con Docker, Railway usará tu `Dockerfile` automáticamente.

---

## Verificación post-deploy
- Revisa los logs en Railway → Deploys → Logs.
- Accede a la URL pública (`https://<tu-proyecto>.railway.app`) y prueba el flujo:
  1. Seleccionar semestre
  2. Registrar ponentes (botón **Siguiente**)
  3. Completar datos del proyecto (botón **Finalizar Inscripción**)
- Comprueba en la BD que `datos_proyectos` y `datos_ponentes` se han creado y que `id_proyect` fue asignado según `registration_token`.

---

## Errores comunes y soluciones
- Error de conexión `SQLSTATE[HY000] [2002]`: revisa DB_HOST/DB_PORT/DB_USER/DB_PASS.
- Si ves errores de permisos al importar SQL, asegúrate de que el usuario tiene privilegios para crear tablas y triggers.
- Si la web falla por rutas, confirma que `BASE_URL` (opcional) esté establecida correctamente.

---

¿Quieres que genere un pequeño script `deploy.sh` con los comandos de importación y push a GitHub, o que intente automatizar la importación en Railway si me proporcionas acceso/credenciales?"}