-- schema.sql (MySQL) — esquema para la aplicación

DROP DATABASE IF EXISTS rueda_proyectos;
CREATE DATABASE rueda_proyectos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rueda_proyectos;

-- Tabla de proyectos (la app usa `datos_proyectos` con PK `id_proyect`)
CREATE TABLE datos_proyectos (
    id_proyect BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    linea VARCHAR(150) DEFAULT NULL,
    fase VARCHAR(100) DEFAULT NULL,
    enfoque VARCHAR(150) DEFAULT NULL,
    asignaturas TEXT DEFAULT NULL,
    aportes TEXT DEFAULT NULL,
    titulo VARCHAR(255) DEFAULT NULL,
    introduccion TEXT DEFAULT NULL,
    problema TEXT DEFAULT NULL,
    justificacion TEXT DEFAULT NULL,
    objetivog TEXT DEFAULT NULL,
    objetivoe TEXT DEFAULT NULL,
    referentes TEXT DEFAULT NULL,
    metodologia TEXT DEFAULT NULL,
    resultados TEXT DEFAULT NULL,
    conclusiones TEXT DEFAULT NULL,
    bibliografia TEXT DEFAULT NULL,
    feedback TEXT DEFAULT NULL,
    semestre TINYINT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_proyect),
    INDEX idx_semestre (semestre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de ponentes (la app usa `datos_ponentes` con PK `id_ponent`)
CREATE TABLE datos_ponentes (
    id_ponent BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    docente VARCHAR(255) DEFAULT NULL,
    nombres VARCHAR(120) NOT NULL,
    apellidos VARCHAR(120) NOT NULL,
    cedula VARCHAR(50) DEFAULT NULL,
    telefono VARCHAR(50) DEFAULT NULL,
    semestre TINYINT UNSIGNED DEFAULT NULL,
    jornada VARCHAR(50) DEFAULT NULL,
    correo VARCHAR(190) DEFAULT NULL,
    registration_token VARCHAR(64) DEFAULT NULL,
    id_proyect BIGINT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_ponent),
    INDEX idx_registration_token (registration_token),
    INDEX idx_id_proyect (id_proyect),
    CONSTRAINT fk_ponentes_proyecto FOREIGN KEY (id_proyect) REFERENCES datos_proyectos(id_proyect) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Trigger para evitar reasignación de ponente a otro proyecto
DELIMITER //
CREATE TRIGGER prevent_ponente_reassign
BEFORE UPDATE ON datos_ponentes
FOR EACH ROW
BEGIN
    IF OLD.id_proyect IS NOT NULL AND (NEW.id_proyect IS NULL OR NEW.id_proyect <> OLD.id_proyect) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede reasignar el proyecto de un ponente una vez asignado.';
    END IF;
END;
//
DELIMITER ;

-- Ejemplo de flujo: insertar ponentes por token y luego crear proyecto y asignarlos
INSERT INTO datos_ponentes (nombres, apellidos, correo, telefono, semestre, registration_token) VALUES
('Ana', 'García', 'ana@example.com', '3001112222', 6, 'SESSION123'),
('Luis', 'Pérez', 'luis@example.com', '3002223333', 6, 'SESSION123'),
('María', 'López', 'maria@example.com', '3003334444', 6, 'SESSION123');

START TRANSACTION;
INSERT INTO datos_proyectos (titulo, linea, fase, enfoque, asignaturas, semestre) VALUES
('Proyecto de ejemplo', 'Innovación', 'Propuesta', 'Social', 'Matemáticas;Programación', 6);
SET @project_id = LAST_INSERT_ID();

UPDATE datos_ponentes SET id_proyect = @project_id WHERE registration_token = 'SESSION123' AND id_proyect IS NULL;
COMMIT;

SELECT p.id_ponent, p.nombres, p.apellidos, p.registration_token, p.id_proyect, pr.titulo
FROM datos_ponentes p
LEFT JOIN datos_proyectos pr ON p.id_proyect = pr.id_proyect
WHERE p.registration_token = 'SESSION123';