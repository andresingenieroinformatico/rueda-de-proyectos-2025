<?php
$title = $proyecto['titulo'] ?? 'Proyecto';
ob_start();
?>
<h1><?= htmlspecialchars($proyecto['titulo']) ?></h1>
<p><strong>Fase:</strong> <?= htmlspecialchars($proyecto['fase']) ?></p>
<p><strong>Introducción:</strong> <?= htmlspecialchars($proyecto['introduccion']) ?></p>
<p><strong>Problema:</strong> <?= htmlspecialchars($proyecto['problema']) ?></p>
<p><strong>Justificación:</strong> <?= htmlspecialchars($proyecto['justificacion']) ?></p>

<h2>Ponentes</h2>
<ul>
    <?php foreach ($ponentes as $po): ?>
        <?php if ($po['id_proyect'] == $proyecto['id_proyect']): ?>
            <li><?= $po['nombres'] . ' ' . $po['apellidos'] ?> (<?= $po['correo'] ?>)</li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
