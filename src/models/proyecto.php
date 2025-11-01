<?php
// src/models/Proyecto.php
require_once __DIR__ . '/../../config/database/conexion.php';

class Proyecto {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Obtener todos los proyectos
    public function obtenerProyectos() {
        $sql = "SELECT * FROM datos_proyectos ORDER BY id_proyect DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un proyecto por ID
    public function obtenerProyecto($id) {
        $sql = "SELECT * FROM datos_proyectos WHERE id_proyect = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Agregar nuevo proyecto
    public function agregarProyecto($data) {
        $sql = "INSERT INTO datos_proyectos (linea, fase, enfoque, asignaturas, aportes, titulo, introduccion, problema, justificacion, objetivog, objetivoe, referentes, metodologia, resultados, conclusiones, bibliografia, feedback)
                VALUES (:linea, :fase, :enfoque, :asignaturas, :aportes, :titulo, :introduccion, :problema, :justificacion, :objetivog, :objetivoe, :referentes, :metodologia, :resultados, :conclusiones, :bibliografia, :feedback)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }
}
