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
        $this->view('seleccionar_semestre');
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
            $this->view('seleccionar_semestre');
        }
    }

    // --- REGISTRO DE PONENTES ---
public function datos_personales()
{
    // PRIORIDAD: POST (envío) > GET (carga)
    $id_proyect = $_POST['id_proyect'] ?? $_GET['id_proyect'] ?? null;

    if (!$id_proyect) {
        die("Error: No se proporcionó el ID del proyecto. POST: " . print_r($_POST, true));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $docente = $_POST['docente'] ?? '';
        $cantidad = intval($_POST['cantidad'] ?? 0);
        $supabase = new SupabaseClient(SUPABASE_URL, SUPABASE_KEY);

        try {
            for ($i = 1; $i <= $cantidad; $i++) {
                $data = [
                    "docente" => $docente,
                    "nombres" => $_POST["nombres{$i}"] ?? '',
                    "apellidos" => $_POST["apellidos{$i}"] ?? '',
                    "cedula" => $_POST["cedula{$i}"] ?? '',
                    "telefono" => $_POST["telefono{$i}"] ?? '',
                    "semestre" => $_POST["semestre{$i}"] ?? '',
                    "jornada" => $_POST["jornada{$i}"] ?? '',
                    "correo" => $_POST["correo{$i}"] ?? '',
                    "id_proyect" => $id_proyect
                ];

                $supabase->insert('datos_ponentes', $data);
            }

            header("Location: index.php?controller=home&action=finalizacion");
            exit;
        } catch (Exception $e) {
            echo "Error al registrar los ponentes: " . $e->getMessage();
        }
    } else {
        $this->view('datos_personales');
    }
}
    public function inscripcion_1()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $supabase = new SupabaseClient(SUPABASE_URL, SUPABASE_KEY);

            try {
                $dataProyecto = [
                    "linea" => $_POST["linea"] ?? '',
                    "fase" =>'Propuesta',
                    "enfoque" => $_POST["enfoque"] ?? '',
                    "asignaturas" => $_POST["asignaturas"] ?? '',
                    "aportes" => $_POST["aportes"] ?? '',
                    "titulo" => $_POST["titulo"] ?? '',
                    "problema" => $_POST["problema"] ?? '',
                    "justificacion" => $_POST["justificacion"] ?? '',
                    "objetivog" => $_POST["objetivog"] ?? '',
                    "objetivoe" => $_POST["objetivoe"] ?? '',
                    "referentes" => $_POST["referentes"] ?? '',
                    "metodologia" => $_POST["metodologia"] ?? '',
                    "resultados" => $_POST["resultados"] ?? '',
                    "conclusiones" => $_POST["conclusiones"] ?? '',
                    "bibliografia" => $_POST["bibliografia"] ?? '',
                    "feedback" => $_POST["feedback"] ?? '',
                    "semestre" => 1
                ];
                $response = $supabase->insert('datos_proyectos', $dataProyecto);
                if (empty($response) || !isset($response[0]['id_proyect'])) {
                    throw new Exception("No se pudo crear el registro del proyecto.");
                }
                $id_proyect = $response[0]['id_proyect'];
                header("Location: index.php?controller=home&action=datos_personales&id_proyect={$id_proyect}");
                exit;
            } catch (Exception $e) {
                echo "Error al registrar el proyecto: " . $e->getMessage();
            }
        } else {
            $this->view('inscripcion_1');
        }
    }

    public function inscripcion_2()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $supabase = new SupabaseClient(SUPABASE_URL, SUPABASE_KEY);

            try {
                // Debug: Loguear los datos recibidos si DEBUG está activo
                if (DEBUG) {
                    error_log("POST data recibida en inscripcion_2: " . print_r($_POST, true));
                }

                // Validar y sanitizar los datos
                $semestre = intval($_POST['semestre'] ?? 2);
                if ($semestre < 2 || $semestre > 9) {
                    throw new Exception("Semestre inválido: $semestre");
                }

                // Validar campos requeridos
                $camposRequeridos = ['linea', 'fase', 'enfoque', 'titulo', 'introduccion'];
                foreach ($camposRequeridos as $campo) {
                    if (empty($_POST[$campo])) {
                        throw new Exception("El campo '$campo' es requerido");
                    }
                }

                $dataProyecto = [
                    "linea" => $_POST["linea"] ?? '',
                    "fase" => $_POST["fase"] ?? '',
                    "enfoque" => $_POST["enfoque"] ?? '',
                    "asignaturas" => $_POST["asignaturas"] ?? '',
                    "aportes" => $_POST["aportes"] ?? '',
                    "titulo" => $_POST["titulo"] ?? '',
                    "introduccion" => $_POST["introduccion"] ?? '',
                    "problema" => $_POST["problema"] ?? '',
                    "justificacion" => $_POST["justificacion"] ?? '',
                    "objetivog" => $_POST["objetivog"] ?? '',
                    "objetivoe" => $_POST["objetivoe"] ?? '',
                    "referentes" => $_POST["referentes"] ?? '',
                    "metodologia" => $_POST["metodologia"] ?? '',
                    "resultados" => $_POST["resultados"] ?? '',
                    "conclusiones" => $_POST["conclusiones"] ?? '',
                    "bibliografia" => $_POST["bibliografia"] ?? '',
                    "feedback" => $_POST["feedback"] ?? '',
                    "semestre" => $semestre
                ];
                $response = $supabase->insert('datos_proyectos', $dataProyecto);
                if (empty($response) || !isset($response[0]['id_proyect'])) {
                    throw new Exception("No se pudo crear el registro del proyecto.");
                }
                $id_proyect = $response[0]['id_proyect'];
                header("Location: index.php?controller=home&action=datos_personales&id_proyect={$id_proyect}");
                exit;
            } catch (Exception $e) {
                echo "Error al registrar el proyecto: " . $e->getMessage();
            }
        } else {
            $this->view('inscripcion_2-9');
        }
    }

    public function finalizacion()
    {
        $this->view('finalizacion');
    }
}
