<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Inscripción</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/inscripcion_2.css?">

</head>
<body>

<?php if (!empty($mensaje_resultado)) echo $mensaje_resultado; ?>

<div class="container">
    <h1>Ficha de Inscripción</h1>
    <?php require_once __DIR__ . '/../../config/config.php'; ?>
    <form action="<?= BASE_URL ?>?controller=home&action=inscripcion_1" method="POST">
        <input type="hidden" name="semestre" value="1">
        <h2>Datos del Proyecto</h2>

        <div class="form-group">
            <label>Línea a la que pertenece:</label>
            <div class="linea-radio">
                <input type="radio" name="linea" value="Ingeniería del software" required> Ingeniería del software<br>
                <input type="radio" name="linea" value="Gestión de la Seguridad Informática"> Gestión de la Seguridad Informática<br>
                <input type="radio" name="linea" value="Redes y Telemática"> Redes y Telemática<br>
                <input type="radio" name="linea" value="Ingeniería del Conocimiento"> Ingeniería del Conocimiento<br>
                <input type="radio" name="linea" value="Robótica"> Robótica
            </div>
        </div>

        <div class="form-group">
            <label>Fase de avance:</label>
            <input type="radio" name="fase" value="Propuesta" checked> Propuesta
        </div>

        <div class="form-group">
            <label>Enfoque de trabajo en equipo con las asignaturas:</label>
            <input type="radio" name="enfoque" value="Interdisciplinario" required> Interdisciplinario
            <input type="radio" name="enfoque" value="Multidisciplinario"> Multidisciplinario
            <input type="radio" name="enfoque" value="Transdisciplinario"> Transdisciplinario
        </div>

        <div class="form-group">
            <label for="asignaturas">Asignatura(s) vinculadas:</label>
            <textarea id="asignaturas" name="asignaturas" required></textarea>
        </div>

        <div class="form-group">
            <label for="aportes">Aportes desde las asignaturas que se ven reflejados en el proyecto:</label>
            <textarea id="aportes" name="aportes" required></textarea>
        </div>

        <div class="form-group">
            <label for="titulo">Título:</label>
            <textarea id="titulo" name="titulo" required></textarea>
        </div>

        <div class="form-group">
            <label for="problema">Planteamiento del Problema:</label>
            <textarea id="problema" name="problema" required></textarea>
        </div>

        <div class="form-group">
            <label for="justificacion">Justificación:</label>
            <textarea id="justificacion" name="justificacion" required></textarea>
        </div>

        <div class="form-group">
            <label for="objetivog">Objetivo General:</label>
            <textarea id="objetivog" name="objetivog" required></textarea>
        </div>

        <div class="form-group">
            <label for="objetivoe">Objetivos Específicos:</label>
            <textarea id="objetivoe" name="objetivoe" required></textarea>
        </div>

        <div class="form-group">
            <label for="referentes">Interdisciplinariedad del proyecto:</label>
            <textarea id="referentes" name="referentes" required></textarea>
        </div>

        <div class="form-group">
            <label for="metodologia">Desarrollo de proyectos:</label>
            <textarea id="metodologia" name="metodologia" required></textarea>
        </div>
        <div class="form-group">
            <label for="resultados">Resultados Esperados:</label>
            <textarea id="resultados" name="resultados" required></textarea>
        </div>

        <div class="form-group">
            <label for="conclusiones">Conclusiones:</label>
            <textarea id="conclusiones" name="conclusiones" required></textarea>
        </div>

        <div class="form-group">
            <label for="bibliografia">Bibliografía:</label>
            <textarea id="bibliografia" name="bibliografia"></textarea>
        </div>

        <div class="form-group">
            <label for="feedback">Feedback (Link de la Encuesta en Google Forms):</label>
            <input type="text" id="feedback" name="feedback">
        </div>

        <button type="submit" class="submit-btn">Siguiente</button>
    </form>
</div>

</body>
</html>
