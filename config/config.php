<?php
// config/config.php

// Carga variables de entorno desde .env si existe
$env_path = __DIR__ . '/../.env';
if (file_exists($env_path)) {
    $env = parse_ini_file($env_path);
} else {
    $env = [];
}

// Helper: lee variable de entorno (sistema > .env > default)
function get_env($key, $default = '')
{
    global $env;
    // Prioridad: 1. getenv() (Sistema/Railway), 2. $_ENV, 3. $_SERVER, 4. Archivo .env, 5. Default
    $val = getenv($key);
    if ($val !== false) return $val;
    
    if (isset($_ENV[$key])) return $_ENV[$key];
    if (isset($_SERVER[$key])) return $_SERVER[$key];
    if (isset($env[$key])) return $env[$key];
    
    return $default;
}

// Entorno y modo debug
define('APP_ENV', strtolower(trim((string) get_env('APP_ENV', 'local'))));
define('DEBUG',   filter_var(get_env('DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN));

// Credenciales PDO Supabase (PostgreSQL)
define('SUPABASE_DB_URL',     trim(get_env('SUPABASE_DB_URL',     get_env('DATABASE_URL', ''))));
define('SUPABASE_DB_HOST',    trim(get_env('SUPABASE_DB_HOST',    get_env('PGHOST',       ''))));
define('SUPABASE_DB_PORT',    trim(get_env('SUPABASE_DB_PORT',    get_env('PGPORT',       '5432'))));
define('SUPABASE_DB_NAME',    trim(get_env('SUPABASE_DB_NAME',    get_env('PGDATABASE',   'postgres'))));
define('SUPABASE_DB_USER',    trim(get_env('SUPABASE_DB_USER',    get_env('PGUSER',       ''))));
define('SUPABASE_DB_PASS',    trim(get_env('SUPABASE_DB_PASS',    get_env('PGPASSWORD',   ''))));
define('SUPABASE_DB_SSLMODE', trim(get_env('SUPABASE_DB_SSLMODE', 'require')));

// Verifica que las credenciales mínimas estén presentes
define(
    'SUPABASE_PDO_CONFIGURED',
    SUPABASE_DB_URL !== '' ||
    (
        SUPABASE_DB_HOST !== '' &&
        SUPABASE_DB_NAME !== '' &&
        SUPABASE_DB_USER !== '' &&
        SUPABASE_DB_PASS !== ''
    )
);

// Deteccion de HTTPS para BASE_URL
$_https = (
    (isset($_SERVER['HTTPS'])                && $_SERVER['HTTPS']                === 'on'    ) ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])&& $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
    (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on'    )
);

$_protocol   = $_https ? 'https://' : 'http://';
$_host       = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_baseFolder = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
$_baseFolder = $_baseFolder === '/' ? '' : $_baseFolder;

define('BASE_URL', $_protocol . $_host . $_baseFolder . '/');

if (DEBUG && !SUPABASE_PDO_CONFIGURED) {
    error_log(
        '[config] Credenciales Supabase incompletas. ' .
        'Verifica SUPABASE_DB_HOST, SUPABASE_DB_PORT, SUPABASE_DB_NAME, ' .
        'SUPABASE_DB_USER y SUPABASE_DB_PASS en .env'
    );
}
