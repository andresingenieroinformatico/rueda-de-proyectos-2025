<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ficha de Inscripción</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/inscripcion_2.css" />
</head>
<body>

<?php if (!empty($mensaje_resultado)) echo $mensaje_resultado; ?>
<div class="container">
    <div class="header-container">
        <img src="<?= BASE_URL ?>assets/img/SISINFO3.png" alt="sisinfo" class="final-image">
        <h1>Ficha de Inscripción</h1>
    </div>
    <?php require_once __DIR__ . '/../../config/config.php'; ?>
    <form action="<?= BASE_URL ?>?controller=home&action=inscripcion_2" method="POST">
        <input type="hidden" name="semestre" value="<?= $_GET['semestre'] ?? 2 ?>" />
        <fieldset>
            <legend>Datos del Proyecto</legend>
            <h3>Línea a la que Pertenece</h3>
            <div class="radio-group-cards two-rows">

                <input type="radio" id="linea_software" name="linea" value="Ingeniería del software" required />
                <label class="radio-card" for="linea_software"><strong>Ingeniería del software</strong></label>

                <input type="radio" id="linea_seguridad" name="linea" value="Gestión de la Seguridad Informática" />
                <label class="radio-card" for="linea_seguridad"><strong>Gestión de la Seguridad Informática</strong></label>

                <input type="radio" id="linea_redes" name="linea" value="Redes y Telemática" />
                <label class="radio-card" for="linea_redes"><strong>Redes y Telemática</strong></label>

                <!-- Fila 2: 2 opciones -->
                <input type="radio" id="linea_conocimiento" name="linea" value="Ingeniería del Conocimiento" />
                <label class="radio-card" for="linea_conocimiento"><strong>Ingeniería del Conocimiento</strong></label>

                <input type="radio" id="linea_robotica" name="linea" value="Robótica" />
                <label class="radio-card" for="linea_robotica"><strong>Robótica</strong></label>
            </div>

            <div class="form-group">
                <h3>Fase de avance:</h3>
                <div class="radio-group-vertical">
                    <input type="radio" id="fase_propuesta" name="fase" value="Propuesta" required />
                    <label class="radio-card-detailed" for="fase_propuesta">
                        <span class="card-title">Propuesta</span>
                        <span class="card-description">Proyectos en fase temprana (problema, justificación, objetivos, etc.).</span>
                    </label>

                    <input type="radio" id="fase_desarrollo" name="fase" value="desarrollo" required />
                    <label class="radio-card-detailed" for="fase_desarrollo">
                        <span class="card-title">Desarrollo</span>
                        <span class="card-description">Proyectos en fase intermedia</span>
                    </label>

                    <input type="radio" id="fase_aplicacion" name="fase" value="Aplicacion" required />
                    <label class="radio-card-detailed" for="fase_aplicacion">
                        <span class="card-title">Aplicación</span>
                        <span class="card-description">Proyectos en fase final</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <h3>Enfoque de trabajo en equipo con las asignaturas:</h3>

                <div class="radio-option">
                    <input type="radio" id="enfoque_inter" name="enfoque" value="Interdisciplinario" required />
                    <label for="enfoque_inter">Interdisciplinario</label>
                    <span class="description">Diferentes asignaturas dentro del mismo Programa.</span>
                </div>

                <div class="radio-option">
                    <input type="radio" id="enfoque_multi" name="enfoque" value="Multidisciplinario" />
                    <label for="enfoque_multi">Multidisciplinario</label>
                    <span class="description">Investigadores de diversas disciplinas con hipótesis separadas.</span>
                </div>

                <div class="radio-option">
                    <input type="radio" id="enfoque_trans" name="enfoque" value="Transdisciplinario" />
                    <label for="enfoque_trans">Transdisciplinario</label>
                    <span class="description">Un tema estudiado a través de varias disciplinas para entender mecanismos ocultos.</span>
                </div>

                <div class="radio-option">
                    <input type="radio" id="enfoque_ninguno" name="enfoque" value="Ninguno" />
                    <label for="enfoque_ninguno">Ninguno</label>
                    <span class="description">solo desde una disciplina.</span>
                </div>
            </div>

            <div class="form-group">
                <h3 for="asignaturas">Asignatura(s) vinculadas:</h3>
                <textarea id="asignaturas" name="asignaturas"placeholder="Ej. Programación Avanzada, Bases de Datos" required></textarea>
            </div>

            <div class="form-group">
                <h3 for="aportes">Aportes desde las asignaturas que se ven reflejados en el proyecto:</h3>
                <textarea id="aportes" name="aportes" placeholder="Describir brevemente los aportes que contribuyeron a la formulación, desarrollo y/o aplicación del proyecto."required></textarea>
            </div>

            <div class="form-group">
                <h3 for="titulo">Título:</h3>
                <textarea id="titulo" name="titulo" placeholder="Afirmación precisa que hace referencia al tema del proyecto" required></textarea>
            </div>

            <div class="form-group">
                <h3 for="introduccion">Introducción:</h3>
                <textarea id="introduccion" name="introduccion" placeholder="Descripción breve del tema de investigación o proyecto, dirigido a orientar al lector sobre lo que se pretende desarrollar." required></textarea>
            </div>

            <div class="form-group">
                <h3 for="problema">Planteamiento del Problema:</h3>
                <textarea id="problema" name="problema" placeholder="Breve descripción de la situación problema que soporta el desarrollo del proyecto de aula, este debe incluir también la pregunta problema" required></textarea>
            </div>

            <div class="form-group">
                <h3 for="justificacion">Justificación:</h3>
                <textarea id="justificacion" name="justificacion" placeholder="Explicar brevemente la importancia y la relevancia del proyecto." required></textarea>
            </div>

            <div class="form-group">
                <h3 for="objetivog">Objetivo General:</h3>
                <textarea id="objetivog" name="objetivog" placeholder="Presentación del objetivo general  (Verbo en infinitivo ar, er, ir + ¿qué? + ¿para qué? + ¿cómo?)."  required></textarea>
            </div>

            <div class="form-group">
                <h3 for="objetivoe">Objetivos Específicos:</h3>
                <textarea id="objetivoe" name="objetivoe" placeholder="Presentación delos objetivos específicos  (Verbo en infinitivo ar, er, ir + ¿qué? + ¿para qué? + ¿cómo?)."   required></textarea>
            </div>

            <div class="form-group">
                <h3 for="referentes">Referentes Teóricos:</h3>
                <textarea id="referentes" name="referentes" placeholder="De manera breve proporcione el contexto y la base conceptual para la investigación o desarrollo del proyecto de aula" required></textarea> 
            </div>

            <div class="form-group">
                <h3 for="metodologia">Diseño Metodológico:</h3>
                <textarea id="metodologia" name="metodologia" placeholder="La metodología debe definirse claramente y debe verse reflejada en el desarrollo de los objetivos específicos." required></textarea>
            </div>

            <div class="form-group">
                <h3 for="resultados">Resultados Esperados:</h3>
                <textarea id="resultados" name="resultados" placeholder="Se evaluarán los resultados esperados sea iniciales parciales  o finales"required></textarea>
            </div>

            <div class="form-group">
                <h3 for="conclusiones">Conclusiones:</h3>
                <textarea id="conclusiones" name="conclusiones" placeholder="Al final de la investigación debe existir una conclusión de las lecciones aprendidas." required></textarea>
            </div>

            <div class="form-group">
                <h3 for="bibliografia">Bibliografía:</h3>
                <textarea id="bibliografia" name="bibliografia" placeholder="Tiene las fuentes de información documentadas y propiamente citadas, cumpliendo con la normatividad vigente (ICONTEC y referentes" required></textarea>
            </div>

            <div class="form-group">
                <h3 for="feedback">Feedback (Link de la Encuesta en Google Forms):</h3>
                <textarea type="text" id="feedback" name="feedback" placeholder="Agregue su formulario de Google Forms" required></textarea>
            </div>
        </fieldset>

        <button type="submit" class="submit-btn">Siguiente</button>
    </form>
</div>

</body>
</html>
