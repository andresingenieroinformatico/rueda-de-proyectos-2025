<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripción - Rueda de Proyectos</title>
    <link rel="stylesheet" href="./assets/css/datos_personales.css">
</head>
<body>
    <div class="container">
        <h1>Rueda de Proyectos - Novena Edición</h1>

        <div class="info-box">
            Formulario completo para <strong>4 estudiantes</strong>. Complete todos los campos.
        </div>

        <form action="procesar.php" method="POST">

            <!-- ESTUDIANTE 1 -->
            <div class="student-block">
                <h3>Estudiante 1</h3>
                <div class="form-group">
                    <label for="nombre1">Nombre completo:</label>
                    <input type="text" id="nombre1" name="nombre1" required>
                </div>
                <div class="form-group">
                    <label for="correo1">Correo institucional:</label>
                    <input type="email" id="correo1" name="correo1" placeholder="ejemplo@unipaz.edu.co" required>
                </div>
                <div class="form-group">
                    <label for="id1">Número de identificación:</label>
                    <input type="text" id="id1" name="id1" required>
                </div>
                <div class="form-group">
                    <label for="jornada1">Jornada:</label>
                    <select id="jornada1" name="jornada1" required>
                        <option value="">Seleccione</option>
                        <option value="diurna">Diurna</option>
                        <option value="nocturna">Nocturna</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="telefono1">Teléfono:</label>
                    <input type="tel" id="telefono1" name="telefono1" required>
                </div>
            </div>

            <!-- ESTUDIANTE 2 -->
            <div class="student-block">
                <h3>Estudiante 2</h3>
                <div class="form-group">
                    <label for="nombre2">Nombre completo:</label>
                    <input type="text" id="nombre2" name="nombre2" required>
                </div>
                <div class="form-group">
                    <label for="correo2">Correo institucional:</label>
                    <input type="email" id="correo2" name="correo2" placeholder="ejemplo@unipaz.edu.co" required>
                </div>
                <div class="form-group">
                    <label for="id2">Número de identificación:</label>
                    <input type="text" id="id2" name="id2" required>
                </div>
                <div class="form-group">
                    <label for="jornada2">Jornada:</label>
                    <select id="jornada2" name="jornada2" required>
                        <option value="">Seleccione</option>
                        <option value="diurna">Diurna</option>
                        <option value="nocturna">Nocturna</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="telefono2">Teléfono:</label>
                    <input type="tel" id="telefono2" name="telefono2" required>
                </div>
            </div>

            <!-- ESTUDIANTE 3 -->
            <div class="student-block">
                <h3>Estudiante 3</h3>
                <div class="form-group">
                    <label for="nombre3">Nombre completo:</label>
                    <input type="text" id="nombre3" name="nombre3" required>
                </div>
                <div class="form-group">
                    <label for="correo3">Correo institucional:</label>
                    <input type="email" id="correo3" name="correo3" placeholder="ejemplo@unipaz.edu.co" required>
                </div>
                <div class="form-group">
                    <label for="id3">Número de identificación:</label>
                    <input type="text" id="id3" name="id3" required>
                </div>
                <div class="form-group">
                    <label for="jornada3">Jornada:</label>
                    <select id="jornada3" name="jornada3" required>
                        <option value="">Seleccione</option>
                        <option value="diurna">Diurna</option>
                        <option value="nocturna">Nocturna</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="telefono3">Teléfono:</label>
                    <input type="tel" id="telefono3" name="telefono3" required>
                </div>
            </div>

            <!-- ESTUDIANTE 4 -->
            <div class="student-block">
                <h3>Estudiante 4</h3>
                <div class="form-group">
                    <label for="nombre4">Nombre completo:</label>
                    <input type="text" id="nombre4" name="nombre4" required>
                </div>
                <div class="form-group">
                    <label for="correo4">Correo institucional:</label>
                    <input type="email" id="correo4" name="correo4" placeholder="ejemplo@unipaz.edu.co" required>
                </div>
                <div class="form-group">
                    <label for="id4">Número de identificación:</label>
                    <input type="text" id="id4" name="id4" required>
                </div>
                <div class="form-group">
                    <label for="jornada4">Jornada:</label>
                    <select id="jornada4" name="jornada4" required>
                        <option value="">Seleccione</option>
                        <option value="diurna">Diurna</option>
                        <option value="nocturna">Nocturna</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="telefono4">Teléfono:</label>
                    <input type="tel" id="telefono4" name="telefono4" required>
                </div>
            </div>
            <button type="submit" class="btn-submit">Siguiente</button>
        </form>

        <footer>
            <p>&copy; <?= date('Y') ?> Rueda de Proyectos - UNIPAZ. Todos los derechos reservados.</p>
        </footer>
    </div>

</body>
</html>