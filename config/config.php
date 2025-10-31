<?php
// config/config.php

$env_path = __DIR__ . '/../.env';

if (!file_exists($env_path)) {
    die("Error: No se encontró .env en $env_path");
}

$env = parse_ini_file($env_path);

if (!$env) {
    die("Error: No se pudo leer el archivo .env");
}

// Variables obligatorias
define('SUPABASE_URL', $env['SUPABASE_URL'] ?? '');
define('SUPABASE_ANON_KEY', $env['SUPABASE_ANON_KEY'] ?? '');
define('SUPABASE_SERVICE_KEY', $env['SUPABASE_SERVICE_KEY'] ?? '');
define('DEBUG', filter_var($env['DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN));

// Usar SERVICE_KEY para backend (bypassa RLS)
define('SUPABASE_KEY', SUPABASE_SERVICE_KEY ?: SUPABASE_ANON_KEY);
?>