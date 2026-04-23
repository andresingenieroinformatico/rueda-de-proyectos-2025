<?php
// config/config.php

// Carga variables de entorno desde .env si existe
$env_path = __DIR__ . '/../.env';
if (file_exists($env_path)) {
    $env = parse_ini_file($env_path);
} else {
    $env = [];
}

// Funcion helper para obtener variables de entorno o valores de .env
function get_env($key, $default = '')
{
    global $env;
    return getenv($key) ?: ($_ENV[$key] ?? $_SERVER[$key] ?? $env[$key] ?? $default);
}

function is_valid_supabase_url($url): bool
{
    $url = trim((string) $url);

    if ($url === '') {
        return false;
    }

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }

    $host = parse_url($url, PHP_URL_HOST);
    return is_string($host) && $host !== '';
}

function detect_app_env(): string
{
    $explicitEnv = strtolower(trim((string) get_env('APP_ENV', '')));
    if ($explicitEnv !== '') {
        return $explicitEnv;
    }

    $railwaySignals = [
        getenv('RAILWAY_ENVIRONMENT'),
        getenv('RAILWAY_ENVIRONMENT_NAME'),
        getenv('RAILWAY_PROJECT_ID'),
        getenv('RAILWAY_SERVICE_ID'),
        getenv('RAILWAY_PUBLIC_DOMAIN'),
    ];

    foreach ($railwaySignals as $signal) {
        if (!empty($signal)) {
            return 'production';
        }
    }

    return 'local';
}

// Configuracion de entorno
define('APP_ENV', detect_app_env());
define('DEBUG', filter_var(get_env('DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN));

// Configuracion de Supabase REST (se mantiene por compatibilidad)
define('SUPABASE_URL', trim(get_env('SUPABASE_URL', '')));
define('SUPABASE_ANON_KEY', trim(get_env('SUPABASE_ANON_KEY', '')));
define('SUPABASE_SERVICE_KEY', trim(get_env('SUPABASE_SERVICE_KEY', '')));
define('SUPABASE_KEY', trim(get_env('SUPABASE_KEY', SUPABASE_SERVICE_KEY ?: SUPABASE_ANON_KEY)));
define('SUPABASE_API_CONFIGURED', is_valid_supabase_url(SUPABASE_URL) && SUPABASE_KEY !== '');

// Configuracion PDO para PostgreSQL de Supabase
define('SUPABASE_DB_URL', trim(get_env('SUPABASE_DB_URL', get_env('DATABASE_URL', ''))));
define('SUPABASE_DB_HOST', trim(get_env('SUPABASE_DB_HOST', get_env('PGHOST', ''))));
define('SUPABASE_DB_PORT', trim(get_env('SUPABASE_DB_PORT', get_env('PGPORT', '5432'))));
define('SUPABASE_DB_NAME', trim(get_env('SUPABASE_DB_NAME', get_env('PGDATABASE', 'postgres'))));
define('SUPABASE_DB_USER', trim(get_env('SUPABASE_DB_USER', get_env('PGUSER', ''))));
define('SUPABASE_DB_PASS', trim(get_env('SUPABASE_DB_PASS', get_env('PGPASSWORD', ''))));
define('SUPABASE_DB_SSLMODE', trim(get_env('SUPABASE_DB_SSLMODE', 'require')));

define(
    'SUPABASE_ENV_PRESENT',
    SUPABASE_API_CONFIGURED ||
    SUPABASE_DB_URL !== '' ||
    SUPABASE_DB_HOST !== '' ||
    SUPABASE_DB_USER !== '' ||
    SUPABASE_DB_PASS !== '' ||
    SUPABASE_URL !== '' ||
    SUPABASE_KEY !== ''
);

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

// Permite forzar el backend con DATA_DRIVER=supabase o DATA_DRIVER=mysql
define('DATA_DRIVER', strtolower(get_env('DATA_DRIVER', SUPABASE_ENV_PRESENT ? 'supabase' : 'mysql')));

function should_use_supabase(): bool
{
    return DATA_DRIVER === 'supabase';
}

// Deteccion mejorada del protocolo HTTPS para definir BASE_URL
$https = false;
if (
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
    (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')
) {
    $https = true;
}

$protocol = $https ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

$baseFolder = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
$baseFolder = $baseFolder === '/' ? '' : $baseFolder;

define('BASE_URL', $protocol . $host . $baseFolder . '/');

if (DEBUG && should_use_supabase() && !SUPABASE_PDO_CONFIGURED) {
    error_log(
        'Supabase esta seleccionado como backend, pero faltan credenciales PDO. ' .
        'Define SUPABASE_DB_URL o SUPABASE_DB_HOST, SUPABASE_DB_PORT, SUPABASE_DB_NAME, ' .
        'SUPABASE_DB_USER y SUPABASE_DB_PASS.'
    );
}
