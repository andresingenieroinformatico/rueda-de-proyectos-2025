<?php
// src/models/Ponente.php
require_once __DIR__ . '/../../config/database/conexion.php';

class Ponente {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Obtener todos los ponentes con su proyecto
    public function obtenerPonentes() {
        $sql = "SELECT p.*, pr.titulo 
                FROM datos_ponentes p
                JOIN datos_proyectos pr ON p.id_proyect = pr.id_proyect
                ORDER BY p.id_ponent DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Agregar nuevo ponente
    public function agregarPonente($data) {
        $sql = "INSERT INTO datos_ponentes (fecha, nombres, apellidos, cedula, telefono, semestre, jornada, correo, id_proyect)
                VALUES (:fecha, :nombres, :apellidos, :cedula, :telefono, :semestre, :jornada, :correo, :id_proyect)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }
}
