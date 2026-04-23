<?php
// config/database/mysql_conexion.php
// Mantiene el nombre del archivo por compatibilidad, pero ahora gestiona PDO
// tanto para PostgreSQL de Supabase como para MySQL.

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/conexion.php';

$MYSQL_HOST = get_env('MYSQL_HOST', get_env('DB_HOST', '127.0.0.1'));
$MYSQL_NAME = get_env('MYSQL_NAME', get_env('DB_NAME', 'rueda_proyectos'));
$MYSQL_USER = get_env('MYSQL_USER', get_env('DB_USER', 'root'));
$MYSQL_PASS = get_env('MYSQL_PASS', get_env('DB_PASS', ''));
$MYSQL_PORT = get_env('MYSQL_PORT', get_env('DB_PORT', '3306'));
$MYSQL_CHARSET = get_env('MYSQL_CHARSET', get_env('DB_CHARSET', 'utf8mb4'));

function build_mysql_dsn(): string
{
    global $MYSQL_HOST, $MYSQL_NAME, $MYSQL_PORT, $MYSQL_CHARSET;

    return "mysql:host={$MYSQL_HOST};port={$MYSQL_PORT};dbname={$MYSQL_NAME};charset={$MYSQL_CHARSET}";
}

function mysql_credentials(): array
{
    global $MYSQL_USER, $MYSQL_PASS;

    return [
        'user' => $MYSQL_USER,
        'pass' => $MYSQL_PASS,
    ];
}

function describe_mysql_target(): string
{
    global $MYSQL_HOST, $MYSQL_NAME, $MYSQL_PORT;

    return "{$MYSQL_HOST}:{$MYSQL_PORT}/{$MYSQL_NAME}";
}

function get_db_connection(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    if (should_use_supabase()) {
        if (!extension_loaded('pdo_pgsql')) {
            throw new RuntimeException('La extension pdo_pgsql no esta disponible en PHP.');
        }

        if (!has_supabase_pdo_credentials()) {
            throw new RuntimeException(
                'Faltan credenciales PDO de Supabase. Define SUPABASE_DB_URL o SUPABASE_DB_HOST, ' .
                'SUPABASE_DB_PORT, SUPABASE_DB_NAME, SUPABASE_DB_USER y SUPABASE_DB_PASS.'
            );
        }

        $config = get_supabase_pdo_config();
        $requiredFields = ['host', 'port', 'dbname', 'user', 'pass'];
        foreach ($requiredFields as $field) {
            if (trim((string) ($config[$field] ?? '')) === '') {
                throw new RuntimeException("La configuracion PDO de Supabase esta incompleta: falta {$field}.");
            }
        }

        $dsn = build_supabase_pgsql_dsn($config);

        try {
            $pdo = new PDO($dsn, $config['user'], $config['pass'], $options);

            if (DEBUG) {
                error_log('Conexion PDO establecida a Supabase PostgreSQL en ' . describe_supabase_pgsql_target($config));
            }

            return $pdo;
        } catch (PDOException $e) {
            error_log('Error conexion PDO a Supabase PostgreSQL ' . describe_supabase_pgsql_target($config) . ': ' . $e->getMessage());

            if (DEBUG) {
                echo '<pre>Error conexion PDO Supabase: ' . htmlspecialchars($e->getMessage()) . '</pre>';
            }

            throw $e;
        }
    }

    if (!extension_loaded('pdo_mysql')) {
        throw new RuntimeException('La extension pdo_mysql no esta disponible en PHP.');
    }

    $dsn = build_mysql_dsn();
    $credentials = mysql_credentials();

    try {
        $pdo = new PDO($dsn, $credentials['user'], $credentials['pass'], $options);

        if (DEBUG) {
            error_log('Conexion PDO establecida a MySQL en ' . describe_mysql_target());
        }

        return $pdo;
    } catch (PDOException $e) {
        error_log('Error conexion PDO a MySQL ' . describe_mysql_target() . ': ' . $e->getMessage());

        if (DEBUG) {
            echo '<pre>Error conexion PDO MySQL: ' . htmlspecialchars($e->getMessage()) . '</pre>';
        }

        throw $e;
    }
}

function db(): PDO
{
    return get_db_connection();
}
