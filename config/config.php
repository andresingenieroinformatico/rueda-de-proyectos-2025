<?php
// config/config.php

// En producción (Render) usamos getenv()
// En desarrollo local usamos .env
$env_path = __DIR__ . '/../.env';
if (file_exists($env_path)) {
    $env = parse_ini_file($env_path);
} else {
    $env = [];
}

// Función helper para obtener variables de entorno o valores de .env
function get_env($key, $default = '') {
    return getenv($key) ?: ($_ENV[$key] ?? $_SERVER[$key] ?? $GLOBALS['env'][$key] ?? $default);
}

// Configuración de Supabase
define('SUPABASE_URL', get_env('SUPABASE_URL', 'https://your-project.supabase.co'));
define('SUPABASE_ANON_KEY', get_env('SUPABASE_ANON_KEY', ''));
define('SUPABASE_SERVICE_KEY', get_env('SUPABASE_SERVICE_KEY', ''));
define('SUPABASE_KEY', get_env('SUPABASE_KEY', SUPABASE_SERVICE_KEY ?: SUPABASE_ANON_KEY));

// Debug mode
define('DEBUG', filter_var(get_env('DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN));

// URL Base - Detecta automáticamente en producción
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseFolder = dirname($_SERVER['SCRIPT_NAME']);
$baseFolder = $baseFolder === '/' ? '' : $baseFolder;
define('BASE_URL', $protocol . $host . $baseFolder . '/');

// Admin credentials
define('ADMIN_USER', get_env('ADMIN_USER', 'admin@example.com'));
define('ADMIN_PASS', get_env('ADMIN_PASS', 'changeme'));

if (empty(SUPABASE_URL) || empty(SUPABASE_KEY)) {
    echo 'funciona';
    if (DEBUG) {
        die("Error: SUPABASE_URL o SUPABASE_KEY no están definidos en .env");
    }
}