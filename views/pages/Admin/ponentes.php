<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Estudiantes <?= $semestre ? "del semestre $semestre" : '' ?></h2>

<form method="GET" action="">
    <input type="hidden" name="controller" value="admin">
    <input type="hidden" name="action" value="estudiantes">
    <label>Selecciona un semestre:</label>
    <select name="semestre" onchange="this.form.submit()">
        <option value="">-- Seleccionar --</option>
        <?php for ($i=1; $i<=10; $i++): ?>
            <option value="<?= $i ?>" <?= $semestre == $i ? 'selected' : '' ?>><?= $i ?></option>
        <?php endfor; ?>
    </select>
</form>

<?php if ($estudiantes): ?>
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Semestre</th>
        <th>Proyecto</th>
    </tr>
    <?php foreach ($estudiantes as $e): ?>
        <tr>
            <td><?= $e['id'] ?></td>
            <td><?= htmlspecialchars($e['nombre']) ?></td>
            <td><?= htmlspecialchars($e['apellido']) ?></td>
            <td><?= htmlspecialchars($e['semestre']) ?></td>
            <td><?= htmlspecialchars($e['proyecto']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
<p>No hay estudiantes registrados para este semestre.</p>
<?php endif; ?>
 
</body>
</html>