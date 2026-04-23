<?php
// src/models/modelponent.php

require_once __DIR__ . '/../../config/database/mysql_conexion.php';
require_once __DIR__ . '/../../config/database/conexion.php';

class PonenteModel
{
    private $pdo;
    private $supabase;

    public function __construct()
    {
        // Verificar si usamos Supabase (API REST)
        $this->supabase = null;
        if (function_exists('should_use_supabase') && should_use_supabase()) {
            $this->supabase = supabase();
            if ($this->supabase === null) {
                throw new RuntimeException(
                    'Supabase esta configurado como backend, pero no pudo inicializarse. Revisa SUPABASE_URL y SUPABASE_SERVICE_KEY en Railway.'
                );
            }
        }

        // Usar PDO (PostgreSQL de Supabase o MySQL como fallback)
        // La función db() ahora maneja el fallback automáticamente
        $this->pdo = null;
        if ($this->supabase === null) {
            $this->pdo = db();
        }
    }

    private function supabaseRequest(string $method, string $path, ?array $payload = null, array $extraHeaders = []): array
    {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('La extension cURL no esta disponible en PHP.');
        }

        $url = rtrim(SUPABASE_URL, '/') . $path;

        $headers = array_merge([
            'apikey: ' . SUPABASE_KEY,
            'Authorization: Bearer ' . SUPABASE_KEY,
            'Content-Type: application/json',
            'Prefer: return=representation',
        ], $extraHeaders);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($payload !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception('cURL Error: ' . $error . ' | URL: ' . $url);
        }

        $decoded = json_decode((string) $response, true);

        if ($httpCode >= 400) {
            $message = $decoded['message'] ?? ('HTTP ' . $httpCode);
            throw new Exception('Supabase Error: ' . $message . ' | URL: ' . $url);
        }

        return [
            'status' => $httpCode,
            'data' => $decoded,
        ];
    }

    public function getAll()
    {
        if ($this->supabase) {
            $response = $this->supabase
                ->from('datos_ponentes')
                ->select('id_ponent,created_at,nombres,apellidos,cedula,telefono,semestre,jornada,correo,id_proyect,datos_proyectos(titulo)')
                ->order('id_ponent', false)
                ->execute();

            return is_array($response) ? $response : [];
        }

        // Usar PDO para PostgreSQL de Supabase o MySQL
        if ($this->pdo) {
            $sql = 'SELECT p.*, pr.titulo AS proyecto_titulo FROM datos_ponentes p LEFT JOIN datos_proyectos pr ON p.id_proyect = pr.id_proyect ORDER BY p.id_ponent DESC';
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        }

        return [];
    }

    public function getBySemestre($semestre)
    {
        if ($this->supabase) {
            $response = $this->supabase
                ->from('datos_ponentes')
                ->select('id_ponent,created_at,nombres,apellidos,cedula,telefono,semestre,jornada,correo,id_proyect,datos_proyectos(titulo)')
                ->eq('semestre', $semestre)
                ->order('id_ponent', false)
                ->execute();

            return is_array($response) ? $response : [];
        }

        // Usar PDO para PostgreSQL de Supabase o MySQL
        if ($this->pdo) {
            $sql = 'SELECT p.*, pr.titulo AS proyecto_titulo FROM datos_ponentes p LEFT JOIN datos_proyectos pr ON p.id_proyect = pr.id_proyect WHERE p.semestre = :semestre ORDER BY p.id_ponent DESC';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':semestre' => $semestre]);
            return $stmt->fetchAll();
        }

        return [];
    }

    public function getById($id)
    {
        if ($this->supabase) {
            $response = $this->supabase
                ->from('datos_ponentes')
                ->select('id_ponent,created_at,nombres,apellidos,cedula,telefono,semestre,jornada,correo,id_proyect,datos_proyectos(titulo)')
                ->eq('id_ponent', $id)
                ->execute();

            return (is_array($response) && !empty($response)) ? $response[0] : null;
        }

        // Usar PDO para PostgreSQL de Supabase o MySQL
        if ($this->pdo) {
            $sql = 'SELECT p.*, pr.titulo AS proyecto_titulo FROM datos_ponentes p LEFT JOIN datos_proyectos pr ON p.id_proyect = pr.id_proyect WHERE p.id_ponent = :id LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch() ?: null;
        }

        return null;
    }

    public function insert(array $data)
    {
        if ($this->supabase) {
            $result = $this->supabaseRequest(
                'POST',
                '/rest/v1/datos_ponentes?select=id_ponent',
                $data
            );

            return $result['data'] ?: false;
        }

        // Usar PDO para PostgreSQL de Supabase o MySQL
        if ($this->pdo) {
            $fields = [];
            $placeholders = [];
            $params = [];

            foreach ($data as $key => $value) {
                $fields[] = $key;
                $placeholders[] = ':' . $key;
                $params[':' . $key] = $value;
            }

            $sql = 'INSERT INTO datos_ponentes (' . implode(',', $fields) . ') VALUES (' . implode(',', $placeholders) . ')';
            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute($params);

            return $ok ? (int) $this->pdo->lastInsertId() : false;
        }

        return false;
    }

    public function insertMany(array $rows)
    {
        if (empty($rows)) {
            return [];
        }

        if ($this->supabase) {
            $result = $this->supabaseRequest(
                'POST',
                '/rest/v1/datos_ponentes?select=id_ponent',
                $rows
            );

            return is_array($result['data']) ? $result['data'] : [];
        }

        // Usar PDO para PostgreSQL de Supabase o MySQL
        if ($this->pdo) {
            $insertedIds = [];
            $this->pdo->beginTransaction();

            try {
                foreach ($rows as $row) {
                    $insertedIds[] = $this->insert($row);
                }

                $this->pdo->commit();
                return $insertedIds;
            } catch (Throwable $e) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }

                throw $e;
            }
        }

        return [];
    }

    public function update($id, array $data)
    {
        if ($this->supabase) {
            $result = $this->supabaseRequest(
                'PATCH',
                '/rest/v1/datos_ponentes?id_ponent=eq.' . rawurlencode((string) $id) . '&select=id_ponent',
                $data
            );

            return in_array($result['status'], [200, 204], true);
        }

        // Usar PDO para PostgreSQL de Supabase o MySQL
        if ($this->pdo) {
            $sets = [];
            $params = [':id' => $id];

            foreach ($data as $key => $value) {
                $sets[] = $key . ' = :' . $key;
                $params[':' . $key] = $value;
            }

            $sql = 'UPDATE datos_ponentes SET ' . implode(',', $sets) . ' WHERE id_ponent = :id';
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        }

        return false;
    }

    public function assignProjectByToken(string $token, int $projectId)
    {
        if ($this->supabase) {
            $result = $this->supabaseRequest(
                'PATCH',
                '/rest/v1/datos_ponentes?registration_token=eq.' . rawurlencode($token) . '&id_proyect=is.null&select=id_ponent',
                ['id_proyect' => $projectId]
            );

            return in_array($result['status'], [200, 204], true);
        }

        // Usar PDO para PostgreSQL de Supabase o MySQL
        if ($this->pdo) {
            $sql = 'UPDATE datos_ponentes SET id_proyect = :id_proyect WHERE registration_token = :token AND id_proyect IS NULL';
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id_proyect' => $projectId,
                ':token' => $token,
            ]);
        }

        return false;
    }

    public function deleteByRegistrationToken(string $token)
    {
        if ($this->supabase) {
            $result = $this->supabaseRequest(
                'DELETE',
                '/rest/v1/datos_ponentes?registration_token=eq.' . rawurlencode($token),
                null,
                ['Prefer: return=minimal']
            );

            return in_array($result['status'], [200, 204], true);
        }

        // Usar PDO para PostgreSQL de Supabase o MySQL
        if ($this->pdo) {
            $sql = 'DELETE FROM datos_ponentes WHERE registration_token = :token';
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':token' => $token]);
        }

        return false;
    }
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':token' => $token]);
    }

    public function delete($id)
    {
        if ($this->supabase) {
            $result = $this->supabaseRequest(
                'DELETE',
                '/rest/v1/datos_ponentes?id_ponent=eq.' . rawurlencode((string) $id),
                null,
                ['Prefer: return=minimal']
            );

            return in_array($result['status'], [200, 204], true);
        }

        $sql = 'DELETE FROM datos_ponentes WHERE id_ponent = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
