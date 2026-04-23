<?php
// config/database/mysql_conexion.php

require_once __DIR__ . '/../../config/config.php';

// Lee variables de entorno o constantes en config.php
$DB_HOST = get_env('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
$DB_NAME = get_env('DB_NAME', getenv('DB_NAME') ?: 'rueda_proyectos');
$DB_USER = get_env('DB_USER', getenv('DB_USER') ?: 'root');
$DB_PASS = get_env('DB_PASS', getenv('DB_PASS') ?: '');
$DB_PORT = get_env('DB_PORT', getenv('DB_PORT') ?: '3306');
$DB_CHARSET = get_env('DB_CHARSET', 'utf8mb4');

function get_db_connection()
{
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS, $DB_PORT, $DB_CHARSET;

    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset={$DB_CHARSET}";

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
