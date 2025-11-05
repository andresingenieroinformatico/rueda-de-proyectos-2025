<?php
session_start(); // No importa si no hay login, sirve solo para guardar temporalmente el semestre

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $semestre = intval($_POST['semestre']);

    if ($semestre >= 1 && $semestre <= 9) {
        // Guardar el semestre en una sesión temporal
        $_SESSION['semestre'] = $semestre;

        if ($semestre == 1) {
            header("Location: index.php?controller=home&action=inscripcion_1");
            exit();
        } elseif ($semestre >= 2 && $semestre <= 9) {
            header("Location: index.php?controller=home&action=inscripcion_2");
            exit();
        }
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
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/seleccionar_semestre.css?v=123">
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
