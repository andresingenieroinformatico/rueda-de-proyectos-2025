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

    <?php 
        $id_proyect = $_GET['id_proyect'] ?? null;
        if (!$id_proyect) {
            die("<p style='color:red; text-align:center;'>Error: No se proporcionó el ID del proyecto.</p>");
        }
    ?>

    <form action="index.php?controller=home&action=datos_personales" method="POST">
        <input type="hidden" name="id_proyect" value="<?= htmlspecialchars($id_proyect) ?>">

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
                        <select id="semestre<?= $i ?>" name="semestre<?= $i ?>">
                            <option value="">Seleccione</option>
                            <?php for ($s = 1; $s <= 10; $s++): ?>
                                <option value="<?= $s ?>">Semestre <?= $s ?></option>
                            <?php endfor; ?>
                        </select>
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

        <button type="submit" class="submit-btn">Terminar Inscripción</button>
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
