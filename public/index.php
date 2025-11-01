<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/config.php';

$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// Normalizar el nombre del controlador
$controller = ucfirst(strtolower($controller)); // Ej: 'home' → 'Home'

// Construir ruta al controlador (convención: HomeController.php)
$controllerFile = __DIR__ . '/../src/controllers/' . $controller . 'Controller.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;

    $controllerClass = $controller . 'Controller';
    $controllerInstance = new $controllerClass();

    if (method_exists($controllerInstance, $action)) {
        $controllerInstance->$action();
    } else {
        http_response_code(404);
        echo "Acción '$action' no encontrada en $controllerClass";
    }
} else {
    http_response_code(404);
    echo "Controlador '$controller' no encontrado. Archivo esperado: $controllerFile";
}