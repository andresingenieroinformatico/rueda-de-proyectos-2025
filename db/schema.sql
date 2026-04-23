-- PostgreSQL version

-- ⚠️ En PostgreSQL no se usa DROP DATABASE dentro de la misma conexión
-- Ejecuta esto aparte si lo necesitas:
-- DROP DATABASE rueda_proyectos;

-- Crear tablas directamente (ya debes estar conectado a la BD)

-- Tabla de proyectos
CREATE TABLE projects (
    id BIGSERIAL PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    linea VARCHAR(150),
    fase VARCHAR(100),
    enfoque VARCHAR(150),
    asignaturas TEXT,
    semestre SMALLINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE INDEX idx_semestre ON projects(semestre);

-- Tabla de ponentes
CREATE TABLE ponentes (
    id BIGSERIAL PRIMARY KEY,
    nombres VARCHAR(120) NOT NULL,
    apellidos VARCHAR(120) NOT NULL,
    email VARCHAR(190),
    telefono VARCHAR(50),
    semestre SMALLINT,
    registration_token VARCHAR(64),
    project_id BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE INDEX idx_registration_token ON ponentes(registration_token);
CREATE INDEX idx_project_id ON ponentes(project_id);

ALTER TABLE ponentes
ADD CONSTRAINT fk_ponentes_project
FOREIGN KEY (project_id)
REFERENCES projects(id)
ON DELETE SET NULL
ON UPDATE CASCADE;

-- 🔁 Trigger en PostgreSQL (cambia bastante)
CREATE OR REPLACE FUNCTION prevent_ponente_reassign()
RETURNS TRIGGER AS $$
BEGIN
    IF OLD.project_id IS NOT NULL AND 
    (NEW.project_id IS NULL OR NEW.project_id <> OLD.project_id) THEN
        RAISE EXCEPTION 'No se puede reasignar el proyecto de un ponente una vez asignado.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_prevent_ponente_reassign
BEFORE UPDATE ON ponentes
FOR EACH ROW
EXECUTE FUNCTION prevent_ponente_reassign();

-- ===============================
-- EJEMPLO DE USO
-- ===============================

INSERT INTO ponentes (nombres, apellidos, email, telefono, semestre, registration_token) VALUES
('Ana', 'García', 'ana@example.com', '3001112222', 6, 'SESSION123'),
('Luis', 'Pérez', 'luis@example.com', '3002223333', 6, 'SESSION123'),
('María', 'López', 'maria@example.com', '3003334444', 6, 'SESSION123');

-- Transacción
BEGIN;

INSERT INTO projects (titulo, linea, fase, enfoque, asignaturas, semestre)
VALUES ('Proyecto de ejemplo', 'Innovación', 'Propuesta', 'Social', 'Matemáticas;Programación', 6)
RETURNING id;

-- ⚠️ En PostgreSQL no existe LAST_INSERT_ID()
-- debes capturar el id manualmente o usar WITH

-- Ejemplo práctico:
WITH nuevo_proyecto AS (
    INSERT INTO projects (titulo, linea, fase, enfoque, asignaturas, semestre)
    VALUES ('Proyecto de ejemplo', 'Innovación', 'Propuesta', 'Social', 'Matemáticas;Programación', 6)
    RETURNING id
)
UPDATE ponentes
SET project_id = (SELECT id FROM nuevo_proyecto)
WHERE registration_token = 'SESSION123' AND project_id IS NULL;

COMMIT;

-- Consulta
SELECT p.id AS ponente_id, p.nombres, p.apellidos, p.registration_token, p.project_id, pr.titulo
FROM ponentes p
LEFT JOIN projects pr ON p.project_id = pr.id
WHERE p.registration_token = 'SESSION123';