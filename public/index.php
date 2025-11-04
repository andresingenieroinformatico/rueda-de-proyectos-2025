<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/config.php';

$controller = $_GET['controller'] ?? 'home'; 
$action = $_GET['action'] ?? 'index';

// Normalizar nombres
$controller = ucfirst(strtolower($controller)); // Ej: 'admin' → 'Admin'
$controllerClass = $controller . 'Controller';  // → 'AdminController'
$controllerFile = __DIR__ . '/../src/controllers/' . $controllerClass . '.php';

// Verificar existencia del archivo del controlador
if (!file_exists($controllerFile)) {
    http_response_code(404);
    die("❌ Controlador no encontrado: {$controllerClass}<br>Ruta esperada: {$controllerFile}");
}

require_once $controllerFile;

// Verificar existencia de la clase
if (!class_exists($controllerClass)) {
    http_response_code(500);
    die("Clase '{$controllerClass}' no definida en {$controllerFile}");
}

// Crear instancia del controlador
$controllerInstance = new $controllerClass();

// Verificar y ejecutar acción
if (!method_exists($controllerInstance, $action)) {
    http_response_code(404);
    die("Acción '{$action}' no encontrada en {$controllerClass}");
}

$controllerInstance->$action();
