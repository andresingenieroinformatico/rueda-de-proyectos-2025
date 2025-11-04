<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/ponentes.css">
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
    
    <h2>Ponentes<?= $semestre ? " del semestre $semestre" : '' ?></h2>

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

<table border="1" cellpadding="8" style="width:100%; margin-top:20px;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Semestre</th>
            <th>Proyecto</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($ponentes && count($ponentes) > 0): ?>
            <?php foreach ($ponentes as $e): ?>
                <tr>
                    <td><?= $e['id_ponent'] ?></td>
                    <td><?= htmlspecialchars($e['nombres']) ?></td>
                    <td><?= htmlspecialchars($e['apellidos']) ?></td>
                    <td><?= htmlspecialchars($e['semestre']) ?></td>
                    <td><?= htmlspecialchars($e['id_proyect']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:#888; font-style:italic;">
                    No hay ponentes registrados para este semestre.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
</body>
</html>