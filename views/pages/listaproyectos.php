<?php
$title = "Lista de Proyectos";
ob_start();
?>
<h1>Proyectos Registrados</h1>
<table border="1" cellpadding="8">
    <tr>
        <th>Título</th>
        <th>Línea</th>
        <th>Fase</th>
        <th>Enfoque</th>
    </tr>

    <?php foreach ($proyectos as $p): ?>

        <tr>
            <td><?= htmlspecialchars($p['titulo']) ?></td>
            <td><?= htmlspecialchars($p['linea']) ?></td>
            <td><?= htmlspecialchars($p['fase']) ?></td>
            <td><?= htmlspecialchars($p['enfoque']) ?></td>
        </tr>

    <?php endforeach; ?>
    
</table>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
