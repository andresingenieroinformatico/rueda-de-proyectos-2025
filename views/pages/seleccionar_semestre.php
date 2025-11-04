<?php
// seleccionar_semestre.php



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $semestre = intval($_POST['semestre']);

    // Determinar a qué plantilla se dirige según el semestre
    if ($semestre >= 1 && $semestre <= 3) {
        // Formularios de primer ciclo
        header("Location: index.php?controller=home&action=inscripcion_1&id_ponent=$id_ponent&semestre=$semestre");
        exit();
    } elseif ($semestre >= 4 && $semestre <= 9) {
        // Formularios de segundo ciclo
        header("Location: index.php?controller=home&action=inscripcion_2&id_ponent=$id_persona&semestre=$semestre");
        exit();
    } else {
        $error = "Por favor selecciona un semestre válido (1 a 9).";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Semestre</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/seleccionar_semestre.css">
</head>
<body>
    <div class="container">
        <h2>Selecciona tu semestre</h2>

        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST" action="">
            <label for="semestre">¿A qué semestre perteneces?</label>
            <select name="semestre" id="semestre" required>
                <option value="">-- Selecciona un semestre --</option>
                <?php
                for ($i = 1; $i <= 9; $i++) {
                    echo "<option value='$i'>Semestre $i</option>";
                }
                ?>
            </select>

            <button type="submit">Continuar</button>
        </form>
    </div>
</body>
</html>
