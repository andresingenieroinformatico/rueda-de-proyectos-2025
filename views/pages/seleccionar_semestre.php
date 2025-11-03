<?php
// seleccionar_semestre.php



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $semestre = intval($_POST['semestre']);

    // Determinar a qué plantilla se dirige según el semestre
    if ($semestre >= 1 && $semestre <= 3) {
        // Formularios de primer ciclo
        header("Location: index.php?controller=home&action=inscripcion_1&id_persona=$id_persona&semestre=$semestre");
        exit();
    } elseif ($semestre >= 4 && $semestre <= 9) {
        // Formularios de segundo ciclo
        header("Location: index.php?controller=home&action=inscripcion_2&id_persona=$id_persona&semestre=$semestre");
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
    <link rel="stylesheet" href="public/css/estilos.css"> <!-- si tienes CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 350px;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
        }
        label, select, button {
            width: 100%;
            display: block;
            margin-bottom: 15px;
        }
        select {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background: #2c3e50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #34495e;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
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
