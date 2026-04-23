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

    return $url !== '' && $url !== 'https://your-project.supabase.co';
}

// Configuracion de entorno
define('APP_ENV', strtolower(get_env('APP_ENV', getenv('RENDER') ? 'production' : 'local')));
define('DEBUG', filter_var(get_env('DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN));

// Configuracion de Supabase
define('SUPABASE_URL', trim(get_env('SUPABASE_URL', '')));
define('SUPABASE_ANON_KEY', trim(get_env('SUPABASE_ANON_KEY', '')));
define('SUPABASE_SERVICE_KEY', trim(get_env('SUPABASE_SERVICE_KEY', '')));
define('SUPABASE_KEY', trim(get_env('SUPABASE_KEY', SUPABASE_SERVICE_KEY ?: SUPABASE_ANON_KEY)));
define('SUPABASE_CONFIGURED', is_valid_supabase_url(SUPABASE_URL) && SUPABASE_KEY !== '');

// Permite forzar el backend con DATA_DRIVER=supabase o DATA_DRIVER=mysql
define('DATA_DRIVER', strtolower(get_env('DATA_DRIVER', SUPABASE_CONFIGURED ? 'supabase' : 'mysql')));

function should_use_supabase(): bool
{
    return DATA_DRIVER === 'supabase' && SUPABASE_CONFIGURED;
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
