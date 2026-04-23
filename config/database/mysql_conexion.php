<?php
// config/database/mysql_conexion.php
// Nombre conservado por compatibilidad con require_once de los modelos.
// Gestiona exclusivamente la conexion PDO a Supabase (PostgreSQL).

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/conexion.php';

function get_db_connection(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    if (!extension_loaded('pdo_pgsql')) {
        throw new RuntimeException('La extension pdo_pgsql no esta disponible en PHP.');
    }

    if (!SUPABASE_PDO_CONFIGURED) {
        throw new RuntimeException(
            'Faltan credenciales de Supabase. Verifica SUPABASE_DB_HOST, ' .
            'SUPABASE_DB_PORT, SUPABASE_DB_NAME, SUPABASE_DB_USER y SUPABASE_DB_PASS en .env'
        );
    }

    $config = get_supabase_pdo_config();

    foreach (['host', 'port', 'dbname', 'user', 'pass'] as $field) {
        if (trim((string) ($config[$field] ?? '')) === '') {
            throw new RuntimeException("Configuracion Supabase incompleta: falta '{$field}'.");
        }
    }

    $dsn = build_supabase_pgsql_dsn($config);

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $config['user'], $config['pass'], $options);

        if (DEBUG) {
            error_log('[DB] Conexion PDO a Supabase: ' . describe_supabase_pgsql_target($config));
        }

        return $pdo;
    } catch (PDOException $e) {
        error_log('[DB] Error PDO Supabase [' . describe_supabase_pgsql_target($config) . ']: ' . $e->getMessage());

        if (DEBUG) {
            echo '<pre>[DB] Error PDO Supabase: ' . htmlspecialchars($e->getMessage()) . '</pre>';
        }

        throw $e;
    }
}

function db(): PDO
{
    return get_db_connection();
}
