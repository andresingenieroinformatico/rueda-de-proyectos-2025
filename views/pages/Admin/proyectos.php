<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>proyectos</title>
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

<h2>Proyectos <?= $semestre ? "del semestre $semestre" : '' ?></h2>
<form method="GET" action="">
    <input type="hidden" name="controller" value="admin">
    <input type="hidden" name="action" value="proyectos">
    <label>Selecciona un semestre:</label>
    <select name="semestre" onchange="this.form.submit()">
        <option value="">-- Todos --</option>
        <?php for ($i = 1; $i <= 9; $i++): ?>
            <option value="<?= $i ?>" <?= $semestre == $i ? 'selected' : '' ?>><?= $i ?></option>
        <?php endfor; ?>
    </select>
</form>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>ID Proyecto</th>
            <th>Título</th>
            <th>Línea</th>
            <th>Fase</th>
            <th>Enfoque</th>
            <th>Asignaturas</th>
            <th>Semestre</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($datos_proyectos)): ?>
            <?php foreach ($datos_proyectos as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['id_proyect']) ?></td>
                    <td><?= htmlspecialchars($p['titulo'] ?? 'Sin título') ?></td>
                    <td><?= htmlspecialchars($p['linea'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($p['fase'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($p['enfoque'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($p['asignaturas'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($p['semestre'] ?? '—') ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>?controller=admin&action=eliminar&id=<?= urlencode($p['id_proyect']) ?>">🗑️</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" style="text-align:center;">No hay proyectos registrados</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>