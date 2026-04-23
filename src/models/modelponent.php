<?php
// src/models/modelponent.php — implementación PDO

require_once __DIR__ . '/../../config/database/mysql_conexion.php';

class PonenteModel
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    public function getAll()
    {
        $sql = "SELECT p.*, pr.titulo AS proyecto_titulo FROM datos_ponentes p LEFT JOIN datos_proyectos pr ON p.id_proyect = pr.id_proyect ORDER BY p.id_ponent DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getBySemestre($semestre)
    {
        $sql = "SELECT p.*, pr.titulo AS proyecto_titulo FROM datos_ponentes p LEFT JOIN datos_proyectos pr ON p.id_proyect = pr.id_proyect WHERE p.semestre = :semestre ORDER BY p.id_ponent DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':semestre' => $semestre]);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT p.*, pr.titulo AS proyecto_titulo FROM datos_ponentes p LEFT JOIN datos_proyectos pr ON p.id_proyect = pr.id_proyect WHERE p.id_ponent = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
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
        $sql = "DELETE FROM datos_ponentes WHERE id_ponent = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
