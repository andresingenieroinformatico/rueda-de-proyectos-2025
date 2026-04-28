<?php
require_once __DIR__ . '/../models/modelponent.php';
require_once __DIR__ . '/../models/modelproyect.php';

class HomeController
{
    private $ponenteModel;
    private $proyectoModel;

    public function __construct()
    {
        $this->ponenteModel = new PonenteModel();
        $this->proyectoModel = new ProyectoModel();
    }

    private function view($file, array $data = [])
    {
        $path = dirname(__DIR__, 2) . "/views/pages/{$file}.php";
        if (!file_exists($path)) {
            http_response_code(404);
            die(
                defined('DEBUG') && DEBUG
                    ? "Vista no encontrada -> {$path}"
                    : 'La pagina solicitada no esta disponible.'
            );
        }

        extract($data, EXTR_SKIP);
        include $path;
    }

    private function logException(Throwable $e, string $context): void
    {
        error_log(sprintf('[%s] %s in %s:%d', $context, $e->getMessage(), $e->getFile(), $e->getLine()));
    }

    private function buildErrorAlert(string $message): string
    {
        return '<div class="server-message server-message-error">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div>';
    }

    private function formatUserError(string $message, Throwable $e): string
    {
        if (defined('DEBUG') && DEBUG) {
            return $message . ' Detalle: ' . $e->getMessage();
        }

        return $message;
    }

    private function nullIfEmpty($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        $value = trim($value);
        return $value === '' ? null : $value;
    }

    private function buildPonentesPayload(string $docente, int $cantidad, $semestreGlobal, string $token): array
    {
        $rows = [];

        for ($i = 1; $i <= $cantidad; $i++) {
            $rows[] = [
                'docente' => trim($docente),
                'nombres' => trim($_POST["nombres{$i}"] ?? ''),
                'apellidos' => trim($_POST["apellidos{$i}"] ?? ''),
                'cedula' => $this->nullIfEmpty($_POST["cedula{$i}"] ?? null),
                'telefono' => $this->nullIfEmpty($_POST["telefono{$i}"] ?? null),
                'semestre' => $this->nullIfEmpty($semestreGlobal),
                'jornada' => $this->nullIfEmpty($_POST["jornada{$i}"] ?? null),
                'correo' => $this->nullIfEmpty($_POST["correo{$i}"] ?? null),
                'registration_token' => $token,
            ];
        }

        return $rows;
    }

    private function extractProjectId($insertResult): ?int
    {
        if (is_int($insertResult) || ctype_digit((string) $insertResult)) {
            return (int) $insertResult;
        }

        if (is_array($insertResult)) {
            if (isset($insertResult['id_proyect'])) {
                return (int) $insertResult['id_proyect'];
            }

            if (isset($insertResult[0]['id_proyect'])) {
                return (int) $insertResult[0]['id_proyect'];
            }
        }

        return null;
    }

    private function persistProject(array $dataProyecto, ?string $registrationToken, string $viewName, string $context): void
    {
        try {
            $insertResult = $this->proyectoModel->insert($dataProyecto);
            $projectId = $this->extractProjectId($insertResult);

            if (!$projectId) {
                throw new RuntimeException('No fue posible obtener el id del proyecto registrado.');
            }

            if ($registrationToken && !$this->ponenteModel->assignProjectByToken($registrationToken, $projectId)) {
                throw new RuntimeException('No fue posible asociar los ponentes al proyecto.');
            }

            header('Location: ' . BASE_URL . '?controller=home&action=finalizacion', true, 303);
            exit;
        } catch (Throwable $e) {
            $this->logException($e, $context);
            $this->view($viewName, [
                'mensaje_resultado' => $this->buildErrorAlert(
                    $this->formatUserError('No pudimos registrar el proyecto en este momento. Intenta nuevamente en unos minutos.', $e)
                ),
            ]);
        }
    }

    public function index()
    {
        $this->view('datos_personales');
    }

    public function seleccionar_semestre()
    {
        header('Location: ' . BASE_URL . '?controller=home&action=datos_personales', true, 303);
        exit();
    }

    public function datos_personales()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $docente = $_POST['docente'] ?? '';
            $cantidad = intval($_POST['cantidad'] ?? 0);
            $semestreGlobal = $_POST['semestre_global'] ?? null;

            try {
                $token = bin2hex(random_bytes(16));
            } catch (Throwable $e) {
                $token = uniqid('sess_', true);
            }

            try {
                $rows = $this->buildPonentesPayload($docente, $cantidad, $semestreGlobal, $token);
                $inserted = $this->ponenteModel->insertMany($rows);

                if (empty($inserted) || count($inserted) !== count($rows)) {
                    throw new RuntimeException('No fue posible completar el registro de todos los ponentes.');
                }

                $next = $_GET['next'] ?? $_POST['next'] ?? 'inscripcion_1';
                header(
                    'Location: ' . BASE_URL . '?controller=home&action=' . rawurlencode($next) . 
                    '&token=' . rawurlencode($token) . 
                    '&semestre=' . rawurlencode($semestreGlobal),
                    true,
                    303
                );
                exit;
            } catch (Throwable $e) {
                $this->logException($e, 'registro_ponentes');
                $this->view('datos_personales', [
                    'mensaje_resultado' => $this->buildErrorAlert(
                        $this->formatUserError('No pudimos registrar los ponentes en este momento. Intenta nuevamente en unos minutos.', $e)
                    ),
                ]);
                return;
            }
        }

        $this->view('datos_personales');
    }

    public function inscripcion_1()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $registrationToken = $_POST['registration_token'] ?? null;

            $dataProyecto = [
                'linea' => $_POST['linea'] ?? '',
                'fase' => 'Propuesta',
                'enfoque' => $_POST['enfoque'] ?? '',
                'asignaturas' => $_POST['asignaturas'] ?? '',
                'aportes' => $_POST['aportes'] ?? '',
                'titulo' => $_POST['titulo'] ?? '',
                'problema' => $_POST['problema'] ?? '',
                'justificacion' => $_POST['justificacion'] ?? '',
                'objetivog' => $_POST['objetivog'] ?? '',
                'objetivoe' => $_POST['objetivoe'] ?? '',
                'referentes' => $_POST['referentes'] ?? '',
                'metodologia' => $_POST['metodologia'] ?? '',
                'resultados' => $_POST['resultados'] ?? '',
                'conclusiones' => $_POST['conclusiones'] ?? '',
                'bibliografia' => $_POST['bibliografia'] ?? '',
                'feedback' => $_POST['feedback'] ?? '',
                'semestre' => $_POST['semestre'] ?? $_GET['semestre'] ?? 1,
            ];

            $this->persistProject($dataProyecto, $registrationToken, 'inscripcion_1', 'registro_proyecto_semestre_1');
            return;
        }

        $this->view('inscripcion_1');
    }

    public function inscripcion_2()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $registrationToken = $_POST['registration_token'] ?? null;

            $dataProyecto = [
                'linea' => $_POST['linea'] ?? '',
                'fase' => $_POST['fase'] ?? '',
                'enfoque' => $_POST['enfoque'] ?? '',
                'asignaturas' => $_POST['asignaturas'] ?? '',
                'aportes' => $_POST['aportes'] ?? '',
                'titulo' => $_POST['titulo'] ?? '',
                'introduccion' => $_POST['introduccion'] ?? '',
                'problema' => $_POST['problema'] ?? '',
                'justificacion' => $_POST['justificacion'] ?? '',
                'objetivog' => $_POST['objetivog'] ?? '',
                'objetivoe' => $_POST['objetivoe'] ?? '',
                'referentes' => $_POST['referentes'] ?? '',
                'metodologia' => $_POST['metodologia'] ?? '',
                'resultados' => $_POST['resultados'] ?? '',
                'conclusiones' => $_POST['conclusiones'] ?? '',
                'bibliografia' => $_POST['bibliografia'] ?? '',
                'feedback' => $_POST['feedback'] ?? '',
                'semestre' => $_POST['semestre'] ?? $_GET['semestre'] ?? 2,
            ];

            $this->persistProject($dataProyecto, $registrationToken, 'inscripcion_2-9', 'registro_proyecto_semestres_2_9');
            return;
        }

        $this->view('inscripcion_2-9');
    }

    public function finalizacion()
    {
        $this->view('finalizacion');
    }
}
