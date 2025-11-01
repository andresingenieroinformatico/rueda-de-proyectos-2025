<?php

/**
 * FICHA DETALLADA: 
 * Este archivo maneja la lógica de inserción para los semestres del 2 al 9.
 */
$mensaje_resultado = '';
$tabla_proyectos = 'datos_proyectos';
$tabla_ponentes = 'datos_ponentes';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
// ESTOS VALORES REEMPLAZA LOS CON TUS CREDENCIALES REALES
$host = 'YOUR_SUPABASE_HOST.supabase.co';
$port = '5432';
$dbname = 'postgres';
$user = 'postgres';
$password = 'YOUR_SUPABASE_SERVICE_ROLE_KEY';

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

$options = [
PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
$pdo = new PDO($dsn, $user, $password, $options);
$pdo->beginTransaction();

$semestre = (int)($_POST['semestre'] ?? 0);

// Validación específica para este formulario
if ($semestre < 2 || $semestre > 9) {
throw new \PDOException("Error: Semestre inválido para esta ficha. Debe ser de 2 a 9.");
}

$fecha_diligenciamiento = $_POST['fecha_diligenciamiento'] ?? date('Y-m-d');
$jornada = $_POST['jornada'] ?? 'N/A';
$docente_orientador = $_POST['docente_orientador'] ?? 'N/A';

//  Mapeo de Datos del Proyecto Comunes y Detallados
$datos_proyecto = [
// Campos Comunes
'linea'             => $_POST['linea'] ?? null,
'titulo'            => $_POST['titulo'] ?? null,
'justificacion'     => $_POST['justificacion'] ?? null,
'objetivos'         => $_POST['objetivos'] ?? null,
'bibliografia'      => $_POST['bibliografia'] ?? null,
'feedback'          => $_POST['feedback_link'] ?? null,
'asignaturas'       => $_POST['asignaturas_vinculadas'] ?? null,
'aportes'           => $_POST['aportes_asignaturas'] ?? null,
'semestre_pertenece' => $semestre,

// Campos Específicos de Semestre 2-9 (FICHA DETALLADA)
'fase'              => $_POST['fase_avance_o'] ?? null,
'introduccion'      => $_POST['introduccion_o'] ?? null,
'problema'          => $_POST['planteamiento_problema_o'] ?? null,
'referentes'        => $_POST['referente_teorico_o'] ?? null, // Mapeado a Referente Teórico
'metodologia'       => $_POST['diseno_metodologico_o'] ?? null, // Mapeado a Diseño Metodológico
'resultados'        => $_POST['resultados_o'] ?? null,
'conclusiones'      => $_POST['conclusiones_o'] ?? null,
'enfoque'           => $_POST['enfoque_o'] ?? null,
];

// INSERCIÓN EN datos_proyectos 
$columnas_proyectos = implode(', ', array_keys($datos_proyecto));
$marcadores_proyectos = implode(', ', array_map(fn($c) => ':' . $c, array_keys($datos_proyecto)));
$sql_proyecto = "INSERT INTO {$tabla_proyectos} ({$columnas_proyectos}) VALUES ({$marcadores_proyectos}) RETURNING id_proyect";

$stmt_proyecto = $pdo->prepare($sql_proyecto);
foreach ($datos_proyecto as $clave => $valor) {
$stmt_proyecto->bindValue(':' . $clave, $valor, $valor === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
}
$stmt_proyecto->execute();
$id_proyect = $stmt_proyecto->fetchColumn();

// INSERCIÓN EN datos_ponentes Docente y Estudiantes
$sql_ponente = "INSERT INTO {$tabla_ponentes} (fecha, nombres, apellidos, cedula, telefono, semestre, jornada, correo, id_proyect) VALUES (:fecha, :nombres, :apellidos, :cedula, :telefono, :semestre, :jornada, :correo, :id_proyect)";
$stmt_ponente = $pdo->prepare($sql_ponente);

// Docente Orientador
$docente_data = [
'fecha' => $fecha_diligenciamiento,
'nombres' => $docente_orientador,
'apellidos' => 'Orientador',
'cedula' => 'N/A',
'telefono' => 'N/A',
'semestre' => (string)$semestre,
'jornada' => $jornada,
'correo' => 'docente@unipaz.edu.co',
'id_proyect' => $id_proyect
];
$stmt_ponente->execute($docente_data);

// Estudiantes Máximo 4
for ($i = 1; $i <= 4; $i++) {
if (!empty($_POST["nombre_e{$i}"]) && !empty($_POST["id_e{$i}"])) {
$nombre_completo = trim($_POST["nombre_e{$i}"] ?? '');

$estudiante_data = [
    'fecha' => $fecha_diligenciamiento,
    'nombres' => $nombre_completo,
    'apellidos' => 'Estudiante',
    'cedula' => $_POST["id_e{$i}"] ?? '',
    'telefono' => $_POST["tel_e{$i}"] ?? '',
    'semestre' => (string)$semestre,
    'jornada' => $jornada,
    'correo' => $_POST["correo_e{$i}"] ?? '',
    'id_proyect' => $id_proyect
];

$stmt_ponente->execute($estudiante_data);
}
}

$pdo->commit();

$mensaje_resultado = '<div class="success-message">🎉 ¡Inscripción exitosa! El proyecto del Semestre ' . $semestre . ' se ha registrado.</div>';
} catch (\PDOException $e) {
if (isset($pdo) && $pdo->inTransaction()) {
$pdo->rollBack();
}
$mensaje_resultado = '<div class="error-message">❌ Error al guardar los datos: ' . $e->getMessage() . '</div>';
} finally {
$pdo = null;
}
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ficha de Inscripción - 2° a 9° Semestre</title>
<link rel="stylesheet" href="assets/css/inscripcion_2-9.css">
</head>

<body>

<?php
if (!empty($mensaje_resultado)) {
echo $mensaje_resultado;
}
?>

<div class="container">
    <h1>Ficha de Inscripción (2° a 9° Semestre - Detallada)</h1>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

        <h2>1. Datos de los Participantes</h2>

        <div class="form-group">
            <label for="fecha_diligenciamiento">Fecha de diligenciamiento:</label>
            <input type="date" id="fecha_diligenciamiento" name="fecha_diligenciamiento" required value="<?php echo date('Y-m-d'); ?>">
        </div>

        <div class="form-group">
            <label for="docente_orientador">Docente(s) orientadores:</label>
            <input type="text" id="docente_orientador" name="docente_orientador" required>
        </div>

        <div class="form-group">
            <label for="semestre">Semestre (s):</label>
            <select id="semestre" name="semestre" required>
    <option value="">-- Seleccione Semestre --</option>
                    <?php for ($i = 2; $i <= 9; $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?>° Semestre</option>
                        <?php endfor; ?>
               
</select>
        </div>

        <div class="form-group">
            <label for="jornada">Jornada:</label>
            <input type="text" id="jornada" name="jornada" required>
        </div>

        <h3>Estudiantes (Máximo 3)</h3>
        <?php for ($i = 1; $i <= 3; $i++): ?>
            <div class="datos-estudiante">
                    <h4>Estudiante <?php echo $i; ?></h4>
                    <div class="participantes-grid">
                            <div class="form-group">
                                    <label for="nombre_e<?php echo $i; ?>">Nombre Completo:</label>
                                    <input type="text" id="nombre_e<?php echo $i; ?>" name="nombre_e<?php echo $i; ?>" <?php echo ($i === 1) ? 'required' : ''; ?>>
                                </div>
                            <div class="form-group">
                                    <label for="id_e<?php echo $i; ?>">Identificación / N° ID:</label>
                                    <input type="text" id="id_e<?php echo $i; ?>" name="id_e<?php echo $i; ?>" <?php echo ($i === 1) ? 'required' : ''; ?>>
                                </div>
                            <div class="form-group">
                                    <label for="tel_e<?php echo $i; ?>">Teléfono:</label>
                                    <input type="text" id="tel_e<?php echo $i; ?>" name="tel_e<?php echo $i; ?>" <?php echo ($i === 1) ? 'required' : ''; ?>>
                                </div>
                        </div>
                    <div class="form-group">
                            <label for="correo_e<?php echo $i; ?>">Correo institucional (ej. Pepito.perez@unipaz.edu.co):</label>
                            <input type="email" id="correo_e<?php echo $i; ?>" name="correo_e<?php echo $i; ?>" <?php echo ($i === 1) ? 'required' : ''; ?>>
                        </div>
                </div>
        <?php endfor; ?>


        <h2 style="margin-top: 30px;">2. Datos del Proyecto</h2>

        <div class="form-group">
            <label>Línea a la que pertenece:</label>
            <div class="linea-radio">
                    <input type="radio" id="linea_sw" name="linea" value="Ingeniería del software" required> <label for="linea_sw">Ingeniería del software</label><br>
                    <input type="radio" id="linea_si" name="linea" value="Gestión de la Seguridad Informática"> <label for="linea_si">Gestión de la Seguridad Informática</label><br>
                    <input type="radio" id="linea_redes" name="linea" value="Redes y Telemática"> <label for="linea_redes">Redes y Telemática</label><br>
                    <input type="radio" id="linea_ic" name="linea" value="Ingeniería del Conocimiento"> <label for="linea_ic">Ingeniería del Conocimiento</label><br>
                    <input type="radio" id="linea_rob" name="linea" value="Robótica"> <label for="linea_rob">Robótica</label>
                </div>
        </div>

        <div class="form-group">
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" placeholder="Afirmación precisa que hace referencia al tema central" required>
        </div>

        <div class="form-group">
            <label for="justificacion">Justificación:</label>
            <textarea id="justificacion" name="justificacion" placeholder="Explicar brevemente la importancia y la relevancia del proyecto." required></textarea>
        </div>

        <div class="form-group">
            <label for="objetivos">Objetivos (General y Específicos):</label>
            <textarea id="objetivos" name="objetivos" placeholder="Verbo en infinitivo + qué + para qué + cómo." required></textarea>
        </div>

        <div class="form-group">
            <label for="feedback_link">Feedback (Link de Formulario de Google con 3 preguntas):</label>
            <input type="text" id="feedback_link" name="feedback_link" placeholder="Generar Link público." required>
        </div>

<h3>Campos Específicos de la Ficha Detallada</h3>

        <div class="form-group">
            <label>Fase de avance (indique con una “X”):</label>
            <div class="linea-radio">
                <input type="radio" id="fase_propuesta_o" name="fase_avance_o" value="Propuesta" required> <label for="fase_propuesta_o">Propuesta</label><br>
                <input type="radio" id="fase_desarrollo_o" name="fase_avance_o" value="Desarrollo"> <label for="fase_desarrollo_o">Desarrollo</label><br>
                <input type="radio" id="fase_aplicacion_o" name="fase_avance_o" value="Aplicación"> <label for="fase_aplicacion_o">Aplicación</label>
            </div>
        </div>

<div class="form-group">
            <label>Enfoque de trabajo en equipo (Inter/Multi/Transdisciplinario):</label>
            <div class="linea-radio">
                <input type="radio" id="enfoque_inter_o" name="enfoque_o" value="Interdisciplinario" required> <label for="enfoque_inter_o">Interdisciplinario</label>
                <input type="radio" id="enfoque_multi_o" name="enfoque_o" value="Multidisciplinario"> <label for="enfoque_multi_o">Multidisciplinario</label>
                <input type="radio" id="enfoque_trans_o" name="enfoque_o" value="Transdisciplinario"> <label for="enfoque_trans_o">Transdisciplinario</label>
            </div>
        </div>

        <div class="form-group">
            <label for="introduccion_o">Introducción:</label>
            <textarea id="introduccion_o" name="introduccion_o" placeholder="Descripción breve del tema de investigación o proyecto." required></textarea>
        </div>

        <div class="form-group">
            <label for="planteamiento_problema_o">Planteamiento del problema:</label>
            <textarea id="planteamiento_problema_o" name="planteamiento_problema_o" placeholder="Descripción de la situación problema y la pregunta problema." required></textarea>
        </div>

        <div class="form-group">
            <label for="referente_teorico_o">Referente teórico:</label>
            <textarea id="referente_teorico_o" name="referente_teorico_o" placeholder="Contexto y base conceptual para la investigación." required></textarea>
        </div>

        <div class="form-group">
            <label for="diseno_metodologico_o">Diseño metodológico:</label>
            <textarea id="diseno_metodologico_o" name="diseno_metodologico_o" placeholder="Definir la metodología y que se vea reflejada en los objetivos específicos." required></textarea>
        </div>

        <div class="form-group">
            <label for="resultados_o">Resultados (Iniciales/Parciales/Finales):</label>
            <textarea id="resultados_o" name="resultados_o" placeholder="Reflejar el avance o cumplimiento total de los objetivos específicos." required></textarea>
        </div>

        <div class="form-group">
            <label for="conclusiones_o">Conclusiones:</label>
            <textarea id="conclusiones_o" name="conclusiones_o" placeholder="Debe existir una conclusión por objetivo desarrollado." required></textarea>
        </div>
        <div class="form-group">
            <label for="bibliografia">Bibliografía:</label>
            <textarea id="bibliografia" name="bibliografia" placeholder="Fuentes de información documentadas y propiamente citadas (ICONTEC y referentes)." required></textarea>
        </div>

        <div class="form-group">
            <label for="asignaturas_vinculadas">Asignatura (s) vinculadas:</label>
            <input type="text" id="asignaturas_vinculadas" name="asignaturas_vinculadas" required>
        </div>
        <div class="form-group">
            <label for="aportes_asignaturas">Aportes desde las asignaturas que se ven reflejados en el proyecto:</label>
            <textarea id="aportes_asignaturas" name="aportes_asignaturas" placeholder="Describir los aportes desde las diferentes asignaturas a la formulación, desarrollo y/o aplicación." required></textarea>
        </div>


        <button type="submit" class="submit-btn">Inscribir Proyecto (2° a 9° Semestre)</button>
   
</form>
</div>

</body>

</html>