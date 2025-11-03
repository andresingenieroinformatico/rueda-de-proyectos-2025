<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripción - Rueda de Proyectos</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/datos_personales.css">
    <style>
        .student-block { 
            border: 1px solid #ccc; 
            padding: 15px; 
            margin: 10px 0; 
            border-radius: 10px;
            display: none;
        }
        .form-group { margin-bottom: 10px; }
        label { display: block; font-weight: bold; }
        input, select { width: 100%; padding: 6px; }
        .btn-submit {
            margin-top: 15px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-submit:hover { background-color: #0056b3; }
    </style>
</head>
<body>
<div class="container">
    <h1>Rueda de Proyectos - Novena Edición</h1>

    <div class="info-box">
        Complete los datos del docente y del equipo (máximo 4 estudiantes).
    </div>

    <form action="index.php?controller=home&action=datos_personales" method="POST">
        <!-- DOCENTE ORIENTADOR -->
        <div class="form-group">
            <label for="docente">Nombre del docente orientador:</label>
            <input type="text" id="docente" name="docente" required>
        </div>

        <!-- CANTIDAD DE ESTUDIANTES -->
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

        <!-- BLOQUES DE ESTUDIANTES -->
        <?php for ($i = 1; $i <= 4; $i++): ?>
        <div class="student-block" id="student<?= $i ?>">
            <h3>Estudiante <?= $i ?></h3>
            <div class="form-group">
                <label for="nombres<?= $i ?>">Nombres:</label>
                <input type="text" id="nombres<?= $i ?>" name="nombres<?= $i ?>">
            </div>
            <div class="form-group">
                <label for="apellidos<?= $i ?>">Apellidos:</label>
                <input type="text" id="apellidos<?= $i ?>" name="apellidos<?= $i ?>">
            </div>
            <div class="form-group">
                <label for="cedula<?= $i ?>">Cédula:</label>
                <input type="text" id="cedula<?= $i ?>" name="cedula<?= $i ?>">
            </div>
            <div class="form-group">
                <label for="telefono<?= $i ?>">Teléfono:</label>
                <input type="tel" id="telefono<?= $i ?>" name="telefono<?= $i ?>">
            </div>
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
            <div class="form-group">
                <label for="correo<?= $i ?>">Correo institucional:</label>
                <input type="email" id="correo<?= $i ?>" name="correo<?= $i ?>" placeholder="ejemplo@unipaz.edu.co">
            </div>
        </div>
        <?php endfor; ?>

        <button type="submit" class="btn-submit">Siguiente</button>
    </form>

    <footer>
        <p>&copy; <?= date('Y') ?> Rueda de Proyectos - UNIPAZ. Todos los derechos reservados.</p>
    </footer>
</div>

<script>
    const cantidadSelect = document.getElementById('cantidad');
    cantidadSelect.addEventListener('change', function() {
        const cantidad = parseInt(this.value);
        for (let i = 1; i <= 4; i++) {
            const bloque = document.getElementById('student' + i);
            bloque.style.display = i <= cantidad ? 'block' : 'none';
            bloque.querySelectorAll('input, select').forEach(el => {
                el.required = i <= cantidad;
            });
        }
    });
</script>
</body>
</html>
