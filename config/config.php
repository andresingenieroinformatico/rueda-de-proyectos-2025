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

// Configuracion de Supabase
define('SUPABASE_URL', trim(get_env('SUPABASE_URL', '')));
define('SUPABASE_ANON_KEY', trim(get_env('SUPABASE_ANON_KEY', '')));
define('SUPABASE_SERVICE_KEY', trim(get_env('SUPABASE_SERVICE_KEY', '')));
define('SUPABASE_KEY', trim(get_env('SUPABASE_KEY', SUPABASE_SERVICE_KEY ?: SUPABASE_ANON_KEY)));
define(
    'SUPABASE_ENV_PRESENT',
    SUPABASE_URL !== '' || SUPABASE_ANON_KEY !== '' || SUPABASE_SERVICE_KEY !== '' || SUPABASE_KEY !== ''
);
define('SUPABASE_CONFIGURED', is_valid_supabase_url(SUPABASE_URL) && SUPABASE_KEY !== '');

// Permite forzar el backend con DATA_DRIVER=supabase, pdo, o mysql
define('DATA_DRIVER', strtolower(get_env('DATA_DRIVER', (SUPABASE_CONFIGURED || SUPABASE_ENV_PRESENT) ? 'supabase' : 'mysql')));

function should_use_supabase(): bool
{
    return DATA_DRIVER === 'supabase' && SUPABASE_CONFIGURED;
}

// Configuración de PDO para PostgreSQL de Supabase
// Soporta tanto variables individuales como DATABASE_URL de Supabase
define('DATABASE_URL', get_env('DATABASE_URL', ''));
define('DB_HOST', get_env('DB_HOST', ''));
define('DB_PORT', get_env('DB_PORT', '5432'));
define('DB_NAME', get_env('DB_NAME', 'postgres'));
define('DB_USER', get_env('DB_USER', 'postgres'));
define('DB_PASSWORD', get_env('DB_PASSWORD', ''));

// Verificar si PDO está disponible
define('PDO_AVAILABLE', extension_loaded('pdo') && extension_loaded('pdo_pgsql'));

function should_use_pdo(): bool
{
    // Usar PDO si DATA_DRIVER=pdo Y hay configuración de base de datos
    if (DATA_DRIVER !== 'pdo') {
        return false;
    }
    
    // Verificar si hay DATABASE_URL o configuración individual
    $hasDatabaseUrl = DATABASE_URL !== '';
    $hasIndividualConfig = DB_HOST !== '' && DB_USER !== '';
    
    return PDO_AVAILABLE && ($hasDatabaseUrl || $hasIndividualConfig);
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

if (DEBUG && DATA_DRIVER === 'supabase' && !SUPABASE_CONFIGURED) {
    error_log('Supabase no esta configurado correctamente. Revisa SUPABASE_URL y SUPABASE_KEY/SERVICE_KEY.');
}
