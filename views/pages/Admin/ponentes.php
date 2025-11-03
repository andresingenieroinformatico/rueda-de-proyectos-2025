<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ponentes</title>
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
    
    <h2>Ponentes<?= $semestre ? "del semestre $semestre" : '' ?></h2>

<form method="GET" action="">
    <input type="hidden" name="controller" value="admin">
    <input type="hidden" name="action" value="ponentes">
    <label>Selecciona un semestre:</label>
    <select name="semestre" onchange="this.form.submit()">
        <option value="">-- Seleccionar --</option>
        <?php for ($i=1; $i<=10; $i++): ?>
            <option value="<?= $i ?>" <?= $semestre == $i ? 'selected' : '' ?>><?= $i ?></option>
        <?php endfor; ?>
    </select>
</form>

<?php if ($ponentes): ?>
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Semestre</th>
        <th>Proyecto</th>
    </tr>
    <?php foreach ($ponentes as $e): ?>
        <tr>
            <td><?= $e['id_ponent'] ?></td>
            <td><?= htmlspecialchars($e['nombres']) ?></td>
            <td><?= htmlspecialchars($e['apellidos']) ?></td>
            <td><?= htmlspecialchars($e['semestre']) ?></td>
            <td><?= htmlspecialchars($e['id_proyect']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
<p>No hay estudiantes registrados para este semestre.</p>
<?php endif; ?>
</body>
</html>