<?php
// config/config.php

$env_path = __DIR__ . '/../.env';

if (!file_exists($env_path)) {
    die("Error: No se encontró .env en $env_path");
}

$env = parse_ini_file($env_path);

if ($env === false) {
    die("Error: No se pudo leer el archivo .env");
}

define('SUPABASE_URL', $env['SUPABASE_URL'] ?? '');
define('SUPABASE_ANON_KEY', $env['SUPABASE_ANON_KEY'] ?? '');
define('SUPABASE_SERVICE_KEY', $env['SUPABASE_SERVICE_KEY'] ?? '');
define('SUPABASE_KEY', $env['SUPABASE_SERVICE_KEY'] ?? $env['SUPABASE_ANON_KEY'] ?? '');
define('DEBUG', filter_var($env['DEBUG'] ?? 'false', FILTER_VALIDATE_BOOLEAN));
define('BASE_URL', 'http://localhost/Rueda_proyectos/public/');
define('ADMIN_USER', $env['ADMIN_USER'] ?? 'mehandhesgithub@gmail.com');
define('ADMIN_PASS', $env['ADMIN_PASS'] ?? '123456789');

if (empty(SUPABASE_URL) || empty(SUPABASE_KEY)) {
    echo 'funciona';
    if (DEBUG) {
        die("Error: SUPABASE_URL o SUPABASE_KEY no están definidos en .env");
    }
}