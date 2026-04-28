<?php
// src/models/modelproyect.php

require_once __DIR__ . '/../../config/database/mysql_conexion.php';

class ProyectoModel
{
    private ?PDO $pdo = null;

    private function pdo(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = db();
        }

        return $this->pdo;
    }

    private function isPgsql(): bool
    {
        return $this->pdo()->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql';
    }

    private function insertAndReturnId(array $data): int|false
    {
        $fields = [];
        $placeholders = [];
        $params = [];

        foreach ($data as $key => $value) {
            $fields[] = $key;
            $placeholders[] = ':' . $key;
            $params[':' . $key] = $value;
        }

        $sql = 'INSERT INTO datos_proyectos (' . implode(',', $fields) . ') VALUES (' . implode(',', $placeholders) . ')';

        if ($this->isPgsql()) {
            $stmt = $this->pdo()->prepare($sql . ' RETURNING id_proyect');
            $stmt->execute($params);
            $id = $stmt->fetchColumn();
            if ($id === false) {
                throw new RuntimeException("No se pudo obtener el ID del proyecto insertado en Supabase.");
            }
            return (int) $id;
        }

        $stmt = $this->pdo()->prepare($sql);
        $ok = $stmt->execute($params);
        return $ok ? (int) $this->pdo()->lastInsertId() : false;
    }

    public function getAll()
    {
        $sql = 'SELECT id_proyect, linea, fase, enfoque, asignaturas, aportes, titulo, introduccion, problema, justificacion, objetivog, objetivoe, referentes, metodologia, resultados, conclusiones, bibliografia, feedback, semestre, created_at, updated_at FROM datos_proyectos ORDER BY id_proyect DESC';
        $stmt = $this->pdo()->query($sql);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = 'SELECT * FROM datos_proyectos WHERE id_proyect = :id LIMIT 1';
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getBySemestre($semestre)
    {
        $sql = 'SELECT * FROM datos_proyectos WHERE semestre = :semestre ORDER BY id_proyect DESC';
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([':semestre' => $semestre]);
        return $stmt->fetchAll();
    }

    public function insert(array $data)
    {
        return $this->insertAndReturnId($data);
    }

    public function update($id, array $data)
    {
        $sets = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            $sets[] = $key . ' = :' . $key;
            $params[':' . $key] = $value;
        }

        $sql = 'UPDATE datos_proyectos SET ' . implode(',', $sets) . ' WHERE id_proyect = :id';
        $stmt = $this->pdo()->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM datos_proyectos WHERE id_proyect = :id';
        $stmt = $this->pdo()->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
