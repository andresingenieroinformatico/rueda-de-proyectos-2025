<?php
require_once __DIR__ . '/../../config/database/conexion.php';

class HomeController
{
    private function view($file)
    {
        $path = dirname(__DIR__, 2) . "/views/pages/{$file}.php";

        if (!file_exists($path)) {
            die("Vista no encontrada → {$path}");
        }

        include $path;
    }

    public function index()
    {
        $this->view('home');
    }

    public function seleccionar_semestre()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $semestre = intval($_POST['semestre'] ?? 0);

        if ($semestre === 1) {
            header("Location: index.php?controller=home&action=inscripcion_1");
            exit();
        } elseif ($semestre >= 2 && $semestre <= 9) {
            header("Location: index.php?controller=home&action=inscripcion_2");
            exit();
        } else {
            echo "Por favor selecciona un semestre válido.";
        }
    } else {
        // Mostrar la vista con el formulario
        $this->view('seleccionar_semestre');
    }
}


    public function datos_personales()
    {
        // Si el formulario fue enviado
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $docente = $_POST['docente'] ?? '';
            $cantidad = $_POST['cantidad'] ?? 0;

            $supabase = new SupabaseClient(SUPABASE_URL, SUPABASE_KEY);

            try {
                // Guardar cada estudiante según la cantidad seleccionada
                for ($i = 1; $i <= $cantidad; $i++) {
                    $nombres = $_POST["nombres{$i}"] ?? '';
                    $apellidos = $_POST["apellidos{$i}"] ?? '';
                    $cedula = $_POST["cedula{$i}"] ?? '';
                    $telefono = $_POST["telefono{$i}"] ?? '';
                    $semestre = $_POST["semestre{$i}"] ?? '';
                    $jornada = $_POST["jornada{$i}"] ?? '';
                    $correo = $_POST["correo{$i}"] ?? '';

                    // Insertar datos en la tabla ponentes
                    $data = [
                        "docente" => $docente,
                        "nombres" => $nombres,
                        "apellidos" => $apellidos,
                        "cedula" => $cedula,
                        "telefono" => $telefono,
                        "semestre" => $semestre,
                        "jornada" => $jornada,
                        "correo" => $correo
                    ];

                    $response = $supabase->insert('datos_ponentes', $data);
                }

                $primer_semestre = $_POST['semestre1'] ?? 1;

                if ($primer_semestre == 1) {
                    header("Location: index.php?controller=home&action=inscripcion_1");
                } else {
                    header("Location: index.php?controller=home&action=inscripcion_2");
                }
                exit;
            } catch (Exception $e) {
                echo "Error al registrar los datos: " . $e->getMessage();
            }
        } else {
            // Mostrar vista del formulario
            $this->view('datos_personales');
        }
    }

public function inscripcion_1()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once __DIR__ . '/../../config/database/conexion.php';
        $supabase = new SupabaseClient(SUPABASE_URL, SUPABASE_KEY);

        try {
            // --- DATOS DEL PROYECTO ---
            $dataProyecto = [
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
                "feedback" => $_POST["feedback_link"] ?? '',
                "semestre" => 1 // 👈 Campo adicional que mencionas
            ];

            // --- INSERTAR EN datos_proyectos ---
            $response = $supabase->insert('datos_proyectos', $dataProyecto);

            if (empty($response) || !isset($response[0]['id_proyect'])) {
                throw new Exception("No se pudo crear el registro del proyecto.");
            }

            $id_proyect = $response[0]['id_proyect'];

            // --- INSERTAR LOS PONENTES ASOCIADOS ---
            if (!empty($_SESSION['estudiantes'])) {
                foreach ($_SESSION['estudiantes'] as $e) {
                    $dataProyecto = [
                        "fecha" => date('c'),
                        "nombres" => $e['nombres'],
                        "apellidos" => $e['apellidos'],
                        "cedula" => $e['cedula'],
                        "telefono" => $e['telefono'],
                        "semestre" => $e['semestre'],
                        "jornada" => $e['jornada'],
                        "correo" => $e['correo'],
                        "id_proyect" => $id_proyect
                    ];

                    $supabase->insert('datos_ponentes', $dataPonente);
                }
            }

            // ✅ Redirigir a la vista de finalización
            header("Location: index.php?controller=home&action=finalizacion");
            exit;

        } catch (Exception $e) {
            echo "Error al registrar el proyecto: " . $e->getMessage();
        }
    } else {
        // Mostrar el formulario de inscripción
        $this->view('inscripcion_1');
    }
}


    public function inscripcion_2()
    {
        $this->view('inscripcion_2-9');
    }

    public function finalizacion()
    {
        $this->view('finalizacion');
    }
}
