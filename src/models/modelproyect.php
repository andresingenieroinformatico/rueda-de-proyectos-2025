<?php
// src/models/modelproyect.php

require_once __DIR__ . '/../../config/database/mysql_conexion.php';
require_once __DIR__ . '/../../config/database/conexion.php';

class ProyectoModel
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
                ->from('datos_proyectos')
                ->select('id_proyect,linea,fase,enfoque,asignaturas,aportes,titulo,introduccion,problema,justificacion,objetivog,objetivoe,referentes,metodologia,resultados,conclusiones,bibliografia,feedback,semestre,created_at,updated_at')
                ->execute();

            return is_array($response) ? $response : [];
        }

        // Usar PDO para PostgreSQL de Supabase o MySQL
        if ($this->pdo) {
            $sql = 'SELECT id_proyect, linea, fase, enfoque, asignaturas, aportes, titulo, introduccion, problema, justificacion, objetivog, objetivoe, referentes, metodologia, resultados, conclusiones, bibliografia, feedback, semestre, created_at, updated_at FROM datos_proyectos ORDER BY id_proyect DESC';
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        }

        return [];
    }

    public function getById($id)
    {
        if ($this->supabase) {
            $response = $this->supabase
                ->from('datos_proyectos')
                ->select('*')
                ->eq('id_proyect', $id)
                ->execute();

            return (is_array($response) && !empty($response)) ? $response[0] : null;
        }

        $sql = 'SELECT * FROM datos_proyectos WHERE id_proyect = :id LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getBySemestre($semestre)
    {
        if ($this->supabase) {
            $response = $this->supabase
                ->from('datos_proyectos')
                ->select('*')
                ->eq('semestre', $semestre)
                ->execute();

            return is_array($response) ? $response : [];
        }

        $sql = 'SELECT * FROM datos_proyectos WHERE semestre = :semestre ORDER BY id_proyect DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':semestre' => $semestre]);
        return $stmt->fetchAll();
    }

    public function insert(array $data)
    {
        if ($this->supabase) {
            $result = $this->supabaseRequest(
                'POST',
                '/rest/v1/datos_proyectos?select=id_proyect',
                $data
            );

            return $result['data'] ?: false;
        }

        $fields = [];
        $placeholders = [];
        $params = [];

        foreach ($data as $key => $value) {
            $fields[] = $key;
            $placeholders[] = ':' . $key;
            $params[':' . $key] = $value;
        }

        $sql = 'INSERT INTO datos_proyectos (' . implode(',', $fields) . ') VALUES (' . implode(',', $placeholders) . ')';
        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute($params);

        return $ok ? (int) $this->pdo->lastInsertId() : false;
    }

    public function update($id, array $data)
    {
        if ($this->supabase) {
            $result = $this->supabaseRequest(
                'PATCH',
                '/rest/v1/datos_proyectos?id_proyect=eq.' . rawurlencode((string) $id) . '&select=id_proyect',
                $data
            );

            return in_array($result['status'], [200, 204], true);
        }

        $sets = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            $sets[] = $key . ' = :' . $key;
            $params[':' . $key] = $value;
        }

        $sql = 'UPDATE datos_proyectos SET ' . implode(',', $sets) . ' WHERE id_proyect = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        if ($this->supabase) {
            $result = $this->supabaseRequest(
                'DELETE',
                '/rest/v1/datos_proyectos?id_proyect=eq.' . rawurlencode((string) $id),
                null,
                ['Prefer: return=minimal']
            );

            return in_array($result['status'], [200, 204], true);
        }

        $sql = 'DELETE FROM datos_proyectos WHERE id_proyect = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
