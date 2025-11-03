<?php
session_start();

require_once __DIR__ . '/../../config/config.php';

// Verifica que Supabase esté configurado
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // --- DATOS DEL PROYECTO ---
    $data = [
        "linea" => $_POST["linea"] ?? '',
        "fase" => $_POST["fase_avance_1"] ?? 'Propuesta',
        "enfoque" => $_POST["enfoque_1"] ?? '',
        "asignaturas" => $_POST["asignaturas_vinculadas"] ?? '',
        "aportes" => $_POST["aportes_asignaturas"] ?? '',
        "titulo" => $_POST["titulo"] ?? '',
        "introduccion" => $_POST["planteamiento_problema_1"] ?? '',
        "problema" => $_POST["planteamiento_problema_1"] ?? '',
        "justificacion" => $_POST["justificacion"] ?? '',
        "objetivog" => $_POST["objetivos"] ?? '',
        "objetivoe" => $_POST["objetivos"] ?? '',
        "referentes" => $_POST["interdisciplinariedad_1"] ?? '',
        "metodologia" => $_POST["desarrollo_proyectos_1"] ?? '',
        "resultados" => $_POST["resultados_esperados_1"] ?? '',
        "conclusiones" => $_POST["conclusiones_1"] ?? '',
        "bibliografia" => $_POST["bibliografia"] ?? '',
        "feedback" => $_POST["feedback_link"] ?? ''
    ];

    // --- INSERTAR EN datos_proyectos ---
    $url = SUPABASE_URL . "/rest/v1/datos_proyectos";
    $headers = [
        "Content-Type: application/json",
        "apikey: " . SUPABASE_KEY,
        "Authorization: Bearer " . SUPABASE_KEY,
        "Prefer: return=representation"
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
        $response = json_decode($result, true);
        $id_proyect = $response[0]['id_proyect'] ?? null;

        if ($id_proyect) {
            // --- GUARDAR LOS PONENTES ---
            $ponentes_url = SUPABASE_URL . "/rest/v1/datos_ponentes";
            $fecha = date('Y-m-d H:i:s');

            $ponentes_data = [];
            foreach ($_SESSION['estudiantes'] as $e) {
                $ponentes_data[] = [
                    "fecha" => $fecha,
                    "nombres" => $e['nombres'],
                    "apellidos" => $e['apellidos'],
                    "cedula" => $e['cedula'],
                    "telefono" => $e['telefono'],
                    "semestre" => $e['semestre'],
                    "jornada" => $e['jornada'],
                    "correo" => $e['correo'],
                    "id_proyect" => $id_proyect
                ];
            }

            $options_ponentes = [
                "http" => [
                    "header" => implode("\r\n", $headers),
                    "method" => "POST",
                    "content" => json_encode($ponentes_data)
                ]
            ];

            $context_ponentes = stream_context_create($options_ponentes);
            $result_ponentes = file_get_contents($ponentes_url, false, $context_ponentes);

            if ($result_ponentes === FALSE) {
                $mensaje_resultado = "<p style='color:orange;'>Proyecto guardado, pero error al registrar ponentes.</p>";
            } else {
                $mensaje_resultado = "<p style='color:green;'>Proyecto y ponentes registrados correctamente.</p>";
            }
        } else {
            $mensaje_resultado = "<p style='color:red;'>Error: no se obtuvo el ID del proyecto.</p>";
        }
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
    <h1>Ficha de Inscripción</h1>
    <form action="index.php?controller=home&action=datos_personales" method="POST">
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
            <input type="radio" name="fase_avance_1" value="Propuesta" checked>Propuesta</input>
        </div>

        <div class="form-group">
            <label>Enfoque de trabajo:</label>
            <input type="radio" name="enfoque_1" value="Interdisciplinario" required>Interdisciplinario
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
