<?php
require_once __DIR__ . '/../config/database/mysql_conexion.php';

try {
    $pdo = get_db_connection();
    if ($pdo) {
        echo "DB_OK\n";
        // comprobar tablas
        $res = $pdo->query("SHOW TABLES LIKE 'datos_ponentes'");
        $has = $res->rowCount() > 0 ? 'YES' : 'NO';
        echo "HAS_DATOS_PONENTES: $has\n";
    }
} catch (Exception $e) {
    echo "DB_ERR: " . $e->getMessage() . "\n";
}
