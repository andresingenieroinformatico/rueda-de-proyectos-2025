<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripción - Rueda de Proyectos</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/datos_personales.css?v=<?= time() ?>">
</head>
<body>
<div class="container">

    <div class="header-container">
        <img src="<?= BASE_URL ?>assets/img/SISINFO3.png" alt="Logo Rueda de Proyectos" class="final-image">
        <h1>Rueda de Proyectos - Novena Edición</h1>
    </div>

    <p>Por favor completa los siguientes datos para registrar a los participantes del proyecto.</p>

    <?php // Ahora este formulario registra los ponentes primero. Al enviar, se generará un token de sesión y se redirigirá para completar los datos del proyecto. ?>

    <?php $next = $_GET['next'] ?? null; ?>
    <?php $selected_semester = intval($_GET['semestre'] ?? $_POST['semestre'] ?? 0); ?>
    <?php if ($selected_semester): ?>
        <p class="info-semestre">Semestre seleccionado: <strong>Semestre <?= $selected_semester ?></strong></p>
    <?php endif; ?>
    <form action="index.php?controller=home&action=datos_personales" method="POST">
        <?php if ($next): ?><input type="hidden" name="next" value="<?= htmlspecialchars($next) ?>"><?php endif; ?>
        <?php if ($selected_semester): ?><input type="hidden" name="semestre_global" value="<?= $selected_semester ?>"><?php endif; ?>

        <fieldset>
            <legend>Datos del Docente Orientador</legend>
            <div class="form-group">
                <label for="docente">Nombre completo del docente o docentes orientadores:</label>
                <input type="text" id="docente" name="docente" required placeholder="Ej: María Pérez y Juan López">
            </div>
        </fieldset>

        <fieldset>
            <legend>Datos de los Estudiantes</legend>

            <div class="form-group">
                <label for="cantidad">Cantidad de estudiantes del proyecto:</label>
                <select id="cantidad" name="cantidad" required>
                    <option value="">Seleccione</option>
                    <option value="1">1 estudiante</option>
                    <option value="2">2 estudiantes</option>
                    <option value="3">3 estudiantes</option>
                    <option value="4">4 estudiantes</option>
                </select>
            </div>

            <?php for ($i = 1; $i <= 4; $i++): ?>
            <div class="student-block" id="student<?= $i ?>" style="display: none;">
                <h3>Estudiante <?= $i ?></h3>
                <div class="form-group-inline">
                    <div class="form-group">
                        <label for="nombres<?= $i ?>">Nombres:</label>
                        <input type="text" id="nombres<?= $i ?>" name="nombres<?= $i ?>">
                    </div>
                    <div class="form-group">
                        <label for="apellidos<?= $i ?>">Apellidos:</label>
                        <input type="text" id="apellidos<?= $i ?>" name="apellidos<?= $i ?>">
                    </div>
                </div>

                <div class="form-group-inline">
                    <div class="form-group">
                        <label for="cedula<?= $i ?>">Cédula:</label>
                        <input type="number" id="cedula<?= $i ?>" name="cedula<?= $i ?>">
                    </div>
                    <div class="form-group">
                        <label for="telefono<?= $i ?>">Teléfono:</label>
                        <input type="number" id="telefono<?= $i ?>" name="telefono<?= $i ?>">
                    </div>
                </div>

                <div class="form-group-inline">
                    <div class="form-group">
                        <label for="semestre<?= $i ?>">Semestre:</label>
                        <?php if ($selected_semester): ?>
                            <input type="hidden" name="semestre<?= $i ?>" value="<?= $selected_semester ?>">
                            <select id="semestre<?= $i ?>" name="semestre_display<?= $i ?>" disabled>
                                <option value="<?= $selected_semester ?>">Semestre <?= $selected_semester ?></option>
                            </select>
                        <?php else: ?>
                            <select id="semestre<?= $i ?>" name="semestre<?= $i ?>">
                                <option value="">Seleccione</option>
                                <?php for ($s = 1; $s <= 9; $s++): ?>
                                    <option value="<?= $s ?>">Semestre <?= $s ?></option>
                                <?php endfor; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="jornada<?= $i ?>">Jornada:</label>
                        <select id="jornada<?= $i ?>" name="jornada<?= $i ?>">
                            <option value="">Seleccione</option>
                            <option value="diurna">Diurna</option>
                            <option value="nocturna">Nocturna</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="correo<?= $i ?>">Correo institucional:</label>
                    <input type="email" id="correo<?= $i ?>" name="correo<?= $i ?>" placeholder="ejemplo@unipaz.edu.co">
                </div>
                <hr style="margin: 25px 0; border: 0; border-top: 1px solid #ddd;">
            </div>
            <?php endfor; ?>
        </fieldset>

        <button type="submit" class="submit-btn">Siguiente</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    const cantidadSelect = document.getElementById('cantidad');
    const totalEstudiantes = 4;

    cantidadSelect.addEventListener('change', () => {
        const cantidad = parseInt(cantidadSelect.value) || 0;
        for (let i = 1; i <= totalEstudiantes; i++) {
            const bloque = document.getElementById('student' + i);
            bloque.style.display = i <= cantidad ? 'block' : 'none';
        }
    });
});
</script>

</body>
</html>
