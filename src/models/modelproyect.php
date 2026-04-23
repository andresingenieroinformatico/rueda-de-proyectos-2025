<?php
// src/models/ProyectoModel.php — implementación PDO (MySQL)

require_once __DIR__ . '/../../config/database/mysql_conexion.php';

class ProyectoModel
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    public function getAll()
    {
        $sql = "SELECT id_proyect, linea, fase, enfoque, asignaturas, aportes, titulo, introduccion, problema, justificacion, objetivog, objetivoe, referentes, metodologia, resultados, conclusiones, bibliografia, feedback, semestre, created_at, updated_at FROM datos_proyectos ORDER BY id_proyect DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM datos_proyectos WHERE id_proyect = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getBySemestre($semestre)
    {
        $sql = "SELECT * FROM datos_proyectos WHERE semestre = :semestre ORDER BY id_proyect DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':semestre' => $semestre]);
        return $stmt->fetchAll();
    }

    public function insert(array $data)
    {
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
        $sql = "DELETE FROM datos_proyectos WHERE id_proyect = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
