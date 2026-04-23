<?php
// La selección de semestre la procesa el controlador `HomeController::seleccionar_semestre()`.
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

        <form method="POST" action="index.php?controller=home&action=seleccionar_semestre">
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
