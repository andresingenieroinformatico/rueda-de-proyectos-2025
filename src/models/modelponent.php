<?php
// src/models/modelponent.php

require_once __DIR__ . '/../../config/database/mysql_conexion.php';

class PonenteModel
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

        $sql = 'INSERT INTO datos_ponentes (' . implode(',', $fields) . ') VALUES (' . implode(',', $placeholders) . ')';

        if ($this->isPgsql()) {
            $stmt = $this->pdo()->prepare($sql . ' RETURNING id_ponent');
            $stmt->execute($params);
            $id = $stmt->fetchColumn();
            return $id !== false ? (int) $id : false;
        }

        $stmt = $this->pdo()->prepare($sql);
        $ok = $stmt->execute($params);
        return $ok ? (int) $this->pdo()->lastInsertId() : false;
    }

    public function getAll()
    {
        $sql = 'SELECT p.*, pr.titulo AS proyecto_titulo FROM datos_ponentes p LEFT JOIN datos_proyectos pr ON p.id_proyect = pr.id_proyect ORDER BY p.id_ponent DESC';
        $stmt = $this->pdo()->query($sql);
        return $stmt->fetchAll();
    }

    public function getBySemestre($semestre)
    {
        $sql = 'SELECT p.*, pr.titulo AS proyecto_titulo FROM datos_ponentes p LEFT JOIN datos_proyectos pr ON p.id_proyect = pr.id_proyect WHERE p.semestre = :semestre ORDER BY p.id_ponent DESC';
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([':semestre' => $semestre]);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = 'SELECT p.*, pr.titulo AS proyecto_titulo FROM datos_ponentes p LEFT JOIN datos_proyectos pr ON p.id_proyect = pr.id_proyect WHERE p.id_ponent = :id LIMIT 1';
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function insert(array $data)
    {
        return $this->insertAndReturnId($data);
    }

    public function insertMany(array $rows)
    {
        if (empty($rows)) {
            return [];
        }

        $insertedIds = [];
        $pdo = $this->pdo();
        $pdo->beginTransaction();

        try {
            foreach ($rows as $row) {
                $id = $this->insertAndReturnId($row);
                if ($id === false) {
                    throw new RuntimeException('Fallo al insertar un ponente: ' . json_encode($row));
                }
                $insertedIds[] = $id;
            }

            $pdo->commit();
            return $insertedIds;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $e;
        }
    }

    public function update($id, array $data)
    {
        $sets = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            $sets[] = $key . ' = :' . $key;
            $params[':' . $key] = $value;
        }

        $sql = 'UPDATE datos_ponentes SET ' . implode(',', $sets) . ' WHERE id_ponent = :id';
        $stmt = $this->pdo()->prepare($sql);
        return $stmt->execute($params);
    }

    public function assignProjectByToken(string $token, int $projectId)
    {
        $sql = 'UPDATE datos_ponentes SET id_proyect = :id_proyect WHERE registration_token = :token AND id_proyect IS NULL';
        $stmt = $this->pdo()->prepare($sql);
        return $stmt->execute([
            ':id_proyect' => $projectId,
            ':token' => $token,
        ]);
    }

    public function deleteByRegistrationToken(string $token)
    {
        $sql = 'DELETE FROM datos_ponentes WHERE registration_token = :token';
        $stmt = $this->pdo()->prepare($sql);
        return $stmt->execute([':token' => $token]);
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM datos_ponentes WHERE id_ponent = :id';
        $stmt = $this->pdo()->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
