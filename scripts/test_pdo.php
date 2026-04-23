<?php
// scripts/test_pdo.php - Script para probar la conexión PDO a Supabase

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database/conexion.php';

echo "=== Test de conexión PDO a Supabase ===\n\n";

// Mostrar configuración
echo "Configuración:\n";
echo "- DATA_DRIVER: " . DATA_DRIVER . "\n";
echo "- DATABASE_URL: " . (defined('DATABASE_URL') && DATABASE_URL ? '(configurada)' : '(no configurada)') . "\n";
echo "- DB_HOST: " . DB_HOST . "\n";
echo "- DB_PORT: " . DB_PORT . "\n";
echo "- DB_NAME: " . DB_NAME . "\n";
echo "- DB_USER: " . DB_USER . "\n";
echo "- PDO_AVAILABLE: " . (PDO_AVAILABLE ? 'true' : 'false') . "\n";
echo "- should_use_pdo(): " . (should_use_pdo() ? 'true' : 'false') . "\n\n";

// Verificar si PDO está disponible
if (!PDO_AVAILABLE) {
    echo "❌ Error: La extensión pdo_pgsql no está instalada.\n";
    echo "En XAMPP, habilita 'extension=pdo_pgsql' en php.ini.\n";
    echo "En Docker, usa: docker-php-ext-install pdo_pgsql\n";
    exit(1);
}

// Verificar si la configuración es correcta
if (!should_use_pdo()) {
    echo "❌ Error: PDO no está configurado correctamente.\n";
    echo "Asegúrate de que:\n";
    echo "  1. DATA_DRIVER=pdo en .env\n";
    echo "  2. DB_HOST esté configurado (o DATABASE_URL)\n";
    exit(1);
}

// Probar conexión
try {
    $pdo = db();
    
    if ($pdo === null) {
        echo "❌ Error: db() retornó null\n";
        echo "Verifica que DATA_DRIVER=pdo y que las variables DB_* estén configuradas.\n";
        exit(1);
    }
    
    echo "✅ Conexión PDO establecida correctamente.\n\n";
    
    // Probar una consulta simple
    echo "Probando consulta simple...\n";
    $stmt = $pdo->query("SELECT version()");
    $version = $stmt->fetch();
    echo "✅ Versión de PostgreSQL: " . $version['version'] . "\n\n";
    
    // Listar tablas
    echo "Listando tablas...\n";
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
    $tables = $stmt->fetchAll();
    
    if (empty($tables)) {
        echo "⚠️  No se encontraron tablas en la base de datos.\n";
    } else {
        echo "✅ Tablas encontradas:\n";
        foreach ($tables as $table) {
            echo "  - " . $table['table_name'] . "\n";
        }
    }
    
    echo "\n=== Test completado exitosamente ===\n";
    
} catch (PDOException $e) {
    echo "❌ Error de conexión PDO:\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "Código: " . $e->getCode() . "\n";
    echo "\nNota: Si el error es 'Unknown host', significa que tu IP no está autorizada en Supabase.\n";
    echo "Esto es normal en desarrollo local. La conexión funcionará en Railway.\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error general:\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    exit(1);
}