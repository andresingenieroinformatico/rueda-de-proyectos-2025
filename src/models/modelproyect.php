<?php
// src/models/ProyectoModel.php — implementación PDO (MySQL)

require_once __DIR__ . '/../../config/database/mysql_conexion.php';
require_once __DIR__ . '/../../config/database/conexion.php';

class ProyectoModel
{
    private $pdo;
    private $supabase;

    public function __construct()
    {
        // Preferir Supabase si está configurado
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
                ->from('datos_proyectos')
                ->select('id_proyect,linea,fase,enfoque,asignaturas,aportes,titulo,introduccion,problema,justificacion,objetivog,objetivoe,referentes,metodologia,resultados,conclusiones,bibliografia,feedback,semestre')
                ->execute();
            return is_array($response) ? $response : [];
        }

        $sql = "SELECT id_proyect, linea, fase, enfoque, asignaturas, aportes, titulo, introduccion, problema, justificacion, objetivog, objetivoe, referentes, metodologia, resultados, conclusiones, bibliografia, feedback, semestre, created_at, updated_at FROM datos_proyectos ORDER BY id_proyect DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        if ($this->supabase) {
            $response = $this->supabase->from('datos_proyectos')->select('*')->eq('id_proyect', $id)->execute();
            return (is_array($response) && !empty($response)) ? $response[0] : null;
        }

        $sql = "SELECT * FROM datos_proyectos WHERE id_proyect = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getBySemestre($semestre)
    {
        if ($this->supabase) {
            $response = $this->supabase->from('datos_proyectos')->select('*')->eq('semestre', $semestre)->execute();
            return is_array($response) ? $response : [];
        }

        $sql = "SELECT * FROM datos_proyectos WHERE semestre = :semestre ORDER BY id_proyect DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':semestre' => $semestre]);
        return $stmt->fetchAll();
    }

    public function insert(array $data)
    {
        if ($this->supabase) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, SUPABASE_URL . "/rest/v1/datos_proyectos");
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

        $sql = "INSERT INTO datos_proyectos (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
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
            $url = SUPABASE_URL . "/rest/v1/datos_proyectos?id_proyect=eq." . rawurlencode($id);
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

        $sql = "UPDATE datos_proyectos SET " . implode(',', $sets) . " WHERE id_proyect = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        if ($this->supabase) {
            $ch = curl_init();
            $url = SUPABASE_URL . "/rest/v1/datos_proyectos?id_proyect=eq." . rawurlencode($id);
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

        $sql = "DELETE FROM datos_proyectos WHERE id_proyect = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
