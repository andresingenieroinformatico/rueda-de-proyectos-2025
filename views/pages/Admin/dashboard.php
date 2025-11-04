<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Administrador</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/dashboard.css">
</head>
<body>

<nav class="navbar">
    <h2>Panel del Administrador</h2>
    <ul>
        <li><a href="<?= BASE_URL ?>?controller=admin&action=dashboard">Inicio</a></li>
        <li><a href="<?= BASE_URL ?>?controller=admin&action=proyectos">Proyectos</a></li>
        <li><a href="<?= BASE_URL ?>?controller=admin&action=ponentes">Ponentes</a></li>
        <li><a href="<?= BASE_URL ?>?controller=Admin&action=logout">Cerrar Sesión</a></li>
    </ul>
</nav>

<main class="content">
    <h3>Bienvenido, Administrador</h3>
    <p>Selecciona una opción del menú para administrar los registros.</p>
</main>

</body>
</html>
