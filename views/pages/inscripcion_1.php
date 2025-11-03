<?php
// inscripcion_1.php

require_once __DIR__ . '/../../config/config.php';

// Verifica que Supabase esté configurado
if (empty(SUPABASE_URL) || empty(SUPABASE_KEY)) {
    die("Error: Supabase no está configurado correctamente.");
}

// Si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Obtiene los datos del formulario
    $linea = $_POST["linea"] ?? '';
    $fase = $_POST["fase_avance_1"] ?? 'Propuesta';
    $enfoque = $_POST["enfoque_1"] ?? '';
    $asignaturas = $_POST["asignaturas_vinculadas"] ?? '';
    $aportes = $_POST["aportes_asignaturas"] ?? '';
    $titulo = $_POST["titulo"] ?? '';
    $introduccion = $_POST["planteamiento_problema_1"] ?? '';
    $problema = $_POST["planteamiento_problema_1"] ?? '';
    $justificacion = $_POST["justificacion"] ?? '';
    $objetivog = $_POST["objetivos"] ?? '';
    $objetivoe = $_POST["objetivos"] ?? '';
    $referentes = $_POST["interdisciplinariedad_1"] ?? '';
    $metodologia = $_POST["desarrollo_proyectos_1"] ?? '';
    $resultados = $_POST["resultados_esperados_1"] ?? '';
    $conclusiones = $_POST["conclusiones_1"] ?? '';
    $bibliografia = $_POST["bibliografia"] ?? '';
    $feedback = $_POST["feedback_link"] ?? '';

    // Crea el arreglo con los datos
    $data = [
        "linea" => $linea,
        "fase" => $fase,
        "enfoque" => $enfoque,
        "asignaturas" => $asignaturas,
        "aportes" => $aportes,
        "titulo" => $titulo,
        "introduccion" => $introduccion,
        "problema" => $problema,
        "justificacion" => $justificacion,
        "objetivog" => $objetivog,
        "objetivoe" => $objetivoe,
        "referentes" => $referentes,
        "metodologia" => $metodologia,
        "resultados" => $resultados,
        "conclusiones" => $conclusiones,
        "bibliografia" => $bibliografia,
        "feedback" => $feedback
    ];

    // Inserta en Supabase
    $url = SUPABASE_URL . "/rest/v1/datos_proyectos";
    $headers = [
        "Content-Type: application/json",
        "apikey: " . SUPABASE_KEY,
        "Authorization: Bearer " . SUPABASE_KEY
    ];

    $options = [
        "http" => [
            "header" => implode("\r\n", $headers),
            "method" => "POST",
            "content" => json_encode([$data])
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        $mensaje_resultado = "<p style='color:red;'>Error al registrar el proyecto.</p>";
    } else {
        $mensaje_resultado = "<p style='color:green;'>Proyecto registrado correctamente.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Inscripción - 1er Semestre</title>
    <link rel="stylesheet" href="../../public/assets/css/inscripcion_1.css">
</head>
<body>

<?php if (!empty($mensaje_resultado)) echo $mensaje_resultado; ?>

<div class="container">
    <h1>Ficha de Inscripción (1er Semestre - Simplificada)</h1>
    <form action="guardar" method="POST">
        <h2>Datos del Proyecto</h2>

        <div class="form-group">
            <label>Línea:</label>
            <div class="linea-radio">
                <input type="radio" name="linea" value="Ingeniería del software" required> Ingeniería del software<br>
                <input type="radio" name="linea" value="Gestión de la Seguridad Informática"> Gestión de la Seguridad Informática<br>
                <input type="radio" name="linea" value="Redes y Telemática"> Redes y Telemática<br>
                <input type="radio" name="linea" value="Ingeniería del Conocimiento"> Ingeniería del Conocimiento<br>
                <input type="radio" name="linea" value="Robótica"> Robótica
            </div>
        </div>

        <div class="form-group">
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" required>
        </div>

        <div class="form-group">
            <label for="justificacion">Justificación:</label>
            <textarea id="justificacion" name="justificacion" required></textarea>
        </div>

        <div class="form-group">
            <label for="objetivos">Objetivos (General y Específicos):</label>
            <textarea id="objetivos" name="objetivos" required></textarea>
        </div>

        <div class="form-group">
            <label for="feedback_link">Feedback (Link de Formulario):</label>
            <input type="text" id="feedback_link" name="feedback_link" required>
        </div>

        <h3>Campos Específicos</h3>

        <div class="form-group">
            <label>Fase de avance:</label>
            <input type="radio" name="fase_avance_1" value="Propuesta" checked> Propuesta
        </div>

        <div class="form-group">
            <label>Enfoque de trabajo:</label>
            <input type="radio" name="enfoque_1" value="Interdisciplinario" required> Interdisciplinario
            <input type="radio" name="enfoque_1" value="Multidisciplinario"> Multidisciplinario
            <input type="radio" name="enfoque_1" value="Transdisciplinario"> Transdisciplinario
        </div>

        <div class="form-group">
            <label for="planteamiento_problema_1">Planteamiento del problema:</label>
            <textarea id="planteamiento_problema_1" name="planteamiento_problema_1" required></textarea>
        </div>

        <div class="form-group">
            <label for="interdisciplinariedad_1">Interdisciplinariedad:</label>
            <textarea id="interdisciplinariedad_1" name="interdisciplinariedad_1" required></textarea>
        </div>

        <div class="form-group">
            <label for="desarrollo_proyectos_1">Desarrollo de Proyectos:</label>
            <textarea id="desarrollo_proyectos_1" name="desarrollo_proyectos_1" required></textarea>
        </div>

        <div class="form-group">
            <label for="resultados_esperados_1">Resultados Esperados:</label>
            <textarea id="resultados_esperados_1" name="resultados_esperados_1" required></textarea>
        </div>

        <div class="form-group">
            <label for="conclusiones_1">Conclusiones:</label>
            <textarea id="conclusiones_1" name="conclusiones_1" required></textarea>
        </div>

        <div class="form-group">
            <label for="bibliografia">Bibliografía:</label>
            <textarea id="bibliografia" name="bibliografia" required></textarea>
        </div>

        <div class="form-group">
            <label for="asignaturas_vinculadas">Asignaturas vinculadas:</label>
            <input type="text" id="asignaturas_vinculadas" name="asignaturas_vinculadas" required>
        </div>

        <div class="form-group">
            <label for="aportes_asignaturas">Aportes desde las asignaturas:</label>
            <textarea id="aportes_asignaturas" name="aportes_asignaturas" required></textarea>
        </div>

        <button type="submit" class="submit-btn">Inscribir Proyecto</button>
    </form>
</div>

</body>
</html>
