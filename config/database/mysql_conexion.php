<?php
// config/database/mysql_conexion.php

require_once __DIR__ . '/../../config/config.php';

// Variables separadas para MySQL (fallback local)
$MYSQL_HOST = get_env('MYSQL_HOST', '127.0.0.1');
$MYSQL_NAME = get_env('MYSQL_NAME', 'rueda_proyectos');
$MYSQL_USER = get_env('MYSQL_USER', 'root');
$MYSQL_PASS = get_env('MYSQL_PASS', get_env('DB_PASS', ''));
$MYSQL_PORT = get_env('MYSQL_PORT', '3306');
$MYSQL_CHARSET = get_env('MYSQL_CHARSET', get_env('DB_CHARSET', 'utf8mb4'));

function get_db_connection()
{
    global $MYSQL_HOST, $MYSQL_NAME, $MYSQL_USER, $MYSQL_PASS, $MYSQL_PORT, $MYSQL_CHARSET;

    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = "mysql:host={$MYSQL_HOST};port={$MYSQL_PORT};dbname={$MYSQL_NAME};charset={$MYSQL_CHARSET}";

    try {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);

        if (defined('DEBUG') && DEBUG) {
            error_log("Conexion PDO establecida a {$DB_HOST}:{$DB_PORT}/{$DB_NAME}");
        }

        return $pdo;
    } catch (PDOException $e) {
        error_log("Error conexion PDO a {$DB_HOST}:{$DB_PORT}/{$DB_NAME}: " . $e->getMessage());

        if (defined('DEBUG') && DEBUG) {
            echo "<pre>Error conexion PDO: " . htmlspecialchars($e->getMessage()) . "</pre>";
        }

        throw $e;
    }
}

// Funcion de compatibilidad para retornos rapidos
// Solo definir si no existe (para permitir PDO de Supabase)
if (!function_exists('db')) {
    function db()
    {
        return get_db_connection();
    }
}
