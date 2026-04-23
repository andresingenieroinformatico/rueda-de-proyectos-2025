<?php
// src/models/modelponent.php — implementación PDO

require_once __DIR__ . '/../../config/database/mysql_conexion.php';
require_once __DIR__ . '/../../config/database/conexion.php';

class PonenteModel
{
    private $pdo;
    private $supabase;

    public function __construct()
    {
        if (function_exists('supabase') && supabase() !== null) {
            $this->supabase = supabase();
            $this->pdo = null;
        } else {
            $this->pdo = db();
            $this->supabase = null;
        }
    }

    public function getAll()
    {
        if ($this->supabase) {
            $response = $this->supabase
                ->from('datos_ponentes')
                ->select('id_ponent, fecha, nombres, apellidos, cedula, telefono, semestre, jornada, correo, id_proyect, datos_proyectos(titulo)')
                ->order('id_ponent', false)
                ->execute();

            return is_array($response) ? $response : [];
        }

        $sql = "SELECT p.*, pr.titulo AS proyecto_titulo FROM datos_ponentes p LEFT JOIN datos_proyectos pr ON p.id_proyect = pr.id_proyect ORDER BY p.id_ponent DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getBySemestre($semestre)
    {
        if ($this->supabase) {
            $response = $this->supabase
                ->from('datos_ponentes')
                ->select('id_ponent, fecha, nombres, apellidos, cedula, telefono, semestre, jornada, correo, id_proyect, datos_proyectos(titulo)')
                ->eq('semestre', $semestre)
                ->order('id_ponent', false)
                ->execute();

            return is_array($response) ? $response : [];
        }

        $sql = "SELECT p.*, pr.titulo AS proyecto_titulo FROM datos_ponentes p LEFT JOIN datos_proyectos pr ON p.id_proyect = pr.id_proyect WHERE p.semestre = :semestre ORDER BY p.id_ponent DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':semestre' => $semestre]);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        if ($this->supabase) {
            $response = $this->supabase
                ->from('datos_ponentes')
                ->select('id_ponent, fecha, nombres, apellidos, cedula, telefono, semestre, jornada, correo, id_proyect, datos_proyectos(titulo)')
                ->eq('id_ponent', $id)
                ->execute();

            return (is_array($response) && !empty($response)) ? $response[0] : null;
        }

        $sql = "SELECT p.*, pr.titulo AS proyecto_titulo FROM datos_ponentes p LEFT JOIN datos_proyectos pr ON p.id_proyect = pr.id_proyect WHERE p.id_ponent = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function insert(array $data)
    {
        if ($this->supabase) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, SUPABASE_URL . "/rest/v1/datos_ponentes");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "apikey: " . SUPABASE_KEY,
                "Authorization: Bearer " . SUPABASE_KEY,
                "Content-Type: application/json",
                "Prefer: return=representation"
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return ($httpCode === 201) ? json_decode($response, true) : false;
        }

        $fields = [];
        $placeholders = [];
        $params = [];

        foreach ($data as $k => $v) {
            $fields[] = $k;
            $placeholders[] = ":{$k}";
            $params[":{$k}"] = $v;
        }

        $sql = "INSERT INTO datos_ponentes (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute($params);

        if ($ok) {
            return (int)$this->pdo->lastInsertId();
        }
        return false;
    }

    public function update($id, array $data)
    {
        if ($this->supabase) {
            $ch = curl_init();
            $url = SUPABASE_URL . "/rest/v1/datos_ponentes?id_ponent=eq." . rawurlencode($id);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "apikey: " . SUPABASE_KEY,
                "Authorization: Bearer " . SUPABASE_KEY,
                "Content-Type: application/json",
                "Prefer: return=representation"
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return ($httpCode === 200) ? json_decode($response, true) : false;
        }

        $sets = [];
        $params = [':id' => $id];

        foreach ($data as $k => $v) {
            $sets[] = "{$k} = :{$k}";
            $params[":{$k}"] = $v;
        }

        $sql = "UPDATE datos_ponentes SET " . implode(',', $sets) . " WHERE id_ponent = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        if ($this->supabase) {
            $ch = curl_init();
            $url = SUPABASE_URL . "/rest/v1/datos_ponentes?id_ponent=eq." . rawurlencode($id);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "apikey: " . SUPABASE_KEY,
                "Authorization: Bearer " . SUPABASE_KEY,
                "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $httpCode === 204;
        }

        $sql = "DELETE FROM datos_ponentes WHERE id_ponent = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
