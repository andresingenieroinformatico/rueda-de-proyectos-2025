<?php
require_once __DIR__ . '/../../config/database/conexion.php';
require_once __DIR__ . '/../../config/database/mysql_conexion.php';

class HomeController
{
    private function view($file, array $data = [])
    {
        $path = dirname(__DIR__, 2) . "/views/pages/{$file}.php";
        if (!file_exists($path)) {
            http_response_code(404);
            die(
                defined('DEBUG') && DEBUG
                    ? "Vista no encontrada -> {$path}"
                    : "La pagina solicitada no esta disponible."
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

    public function index()
    {
        $this->view('datos_personales');
    }

    public function seleccionar_semestre()
    {
        // La seleccion de semestre ahora se realiza directamente en la vista datos_personales.
        header('Location: index.php?controller=home&action=datos_personales', true, 303);
        exit();
    }

    public function datos_personales()
    {
        // El formulario registra los ponentes primero y genera un token de sesion
        // para completar luego los datos del proyecto.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $docente = $_POST['docente'] ?? '';
            $cantidad = intval($_POST['cantidad'] ?? 0);

            try {
                $token = bin2hex(random_bytes(16));
            } catch (Exception $e) {
                $token = uniqid('sess_', true);
            }

            try {
                $pdo = db();
                $pdo->beginTransaction();

                $sql = 'INSERT INTO datos_ponentes (docente, nombres, apellidos, cedula, telefono, semestre, jornada, correo, registration_token, id_proyect, created_at) VALUES (:docente, :nombres, :apellidos, :cedula, :telefono, :semestre, :jornada, :correo, :registration_token, NULL, NOW())';
                $stmt = $pdo->prepare($sql);

                $semestre_global = $_POST['semestre_global'] ?? null;
                for ($i = 1; $i <= $cantidad; $i++) {
                    $params = [
                        ':docente' => $docente,
                        ':nombres' => $_POST["nombres{$i}"] ?? '',
                        ':apellidos' => $_POST["apellidos{$i}"] ?? '',
                        ':cedula' => $_POST["cedula{$i}"] ?? '',
                        ':telefono' => $_POST["telefono{$i}"] ?? '',
                        ':semestre' => $_POST["semestre{$i}"] ?? $semestre_global,
                        ':jornada' => $_POST["jornada{$i}"] ?? null,
                        ':correo' => $_POST["correo{$i}"] ?? null,
                        ':registration_token' => $token,
                    ];

                    $stmt->execute($params);
                }

                $pdo->commit();

                $next = $_GET['next'] ?? $_POST['next'] ?? 'inscripcion_1';
                header("Location: index.php?controller=home&action={$next}&token={$token}");
                exit;
            } catch (Throwable $e) {
                if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
                    $pdo->rollBack();
                }

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
            $registration_token = $_POST['registration_token'] ?? null;

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
                'semestre' => 1,
            ];

            try {
                $pdo = db();
                $pdo->beginTransaction();

                $fields = array_keys($dataProyecto);
                $placeholders = array_map(fn($f) => ':' . $f, $fields);
                $sql = 'INSERT INTO datos_proyectos (' . implode(',', $fields) . ') VALUES (' . implode(',', $placeholders) . ')';
                $stmt = $pdo->prepare($sql);
                $params = [];
                foreach ($dataProyecto as $k => $v) {
                    $params[':' . $k] = $v;
                }
                $stmt->execute($params);
                $id_proyect = (int) $pdo->lastInsertId();

                if ($registration_token) {
                    $sqlUp = 'UPDATE datos_ponentes SET id_proyect = :id_proyect WHERE registration_token = :token AND (id_proyect IS NULL OR id_proyect = \'\')';
                    $stmt2 = $pdo->prepare($sqlUp);
                    $stmt2->execute([':id_proyect' => $id_proyect, ':token' => $registration_token]);
                }

                $pdo->commit();

                header('Location: index.php?controller=home&action=finalizacion');
                exit;
            } catch (Throwable $e) {
                if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                $this->logException($e, 'registro_proyecto_semestre_1');
                $this->view('inscripcion_1', [
                    'mensaje_resultado' => $this->buildErrorAlert(
                        $this->formatUserError('No pudimos registrar el proyecto en este momento. Intenta nuevamente en unos minutos.', $e)
                    ),
                ]);
                return;
            }
        }

        $this->view('inscripcion_1');
    }

    public function inscripcion_2()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $registration_token = $_POST['registration_token'] ?? null;

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
                'semestre' => 2,
            ];

            try {
                $pdo = db();
                $pdo->beginTransaction();

                $fields = array_keys($dataProyecto);
                $placeholders = array_map(fn($f) => ':' . $f, $fields);
                $sql = 'INSERT INTO datos_proyectos (' . implode(',', $fields) . ') VALUES (' . implode(',', $placeholders) . ')';
                $stmt = $pdo->prepare($sql);
                $params = [];
                foreach ($dataProyecto as $k => $v) {
                    $params[':' . $k] = $v;
                }
                $stmt->execute($params);
                $id_proyect = (int) $pdo->lastInsertId();

                if ($registration_token) {
                    $sqlUp = 'UPDATE datos_ponentes SET id_proyect = :id_proyect WHERE registration_token = :token AND (id_proyect IS NULL OR id_proyect = \'\')';
                    $stmt2 = $pdo->prepare($sqlUp);
                    $stmt2->execute([':id_proyect' => $id_proyect, ':token' => $registration_token]);
                }

                $pdo->commit();

                header('Location: index.php?controller=home&action=finalizacion');
                exit;
            } catch (Throwable $e) {
                if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                $this->logException($e, 'registro_proyecto_semestres_2_9');
                $this->view('inscripcion_2-9', [
                    'mensaje_resultado' => $this->buildErrorAlert(
                        $this->formatUserError('No pudimos registrar el proyecto en este momento. Intenta nuevamente en unos minutos.', $e)
                    ),
                ]);
                return;
            }
        }

        $this->view('inscripcion_2-9');
    }

    public function finalizacion()
    {
        $this->view('finalizacion');
    }
}
