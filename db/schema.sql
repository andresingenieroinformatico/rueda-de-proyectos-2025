-- =============================================================
-- schema.sql — PostgreSQL / Supabase
-- Ejecutar en: Supabase > SQL Editor > New query
-- =============================================================

-- ---------------------------------------------------------------
-- 1. Tabla de proyectos
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS datos_proyectos (
    id_proyect   BIGSERIAL      PRIMARY KEY,
    linea        VARCHAR(150)   DEFAULT NULL,
    fase         VARCHAR(100)   DEFAULT NULL,
    enfoque      VARCHAR(150)   DEFAULT NULL,
    asignaturas  TEXT           DEFAULT NULL,
    aportes      TEXT           DEFAULT NULL,
    titulo       VARCHAR(255)   DEFAULT NULL,
    introduccion TEXT           DEFAULT NULL,
    problema     TEXT           DEFAULT NULL,
    justificacion TEXT          DEFAULT NULL,
    objetivog    TEXT           DEFAULT NULL,
    objetivoe    TEXT           DEFAULT NULL,
    referentes   TEXT           DEFAULT NULL,
    metodologia  TEXT           DEFAULT NULL,
    resultados   TEXT           DEFAULT NULL,
    conclusiones TEXT           DEFAULT NULL,
    bibliografia TEXT           DEFAULT NULL,
    feedback     TEXT           DEFAULT NULL,
    semestre     SMALLINT       DEFAULT NULL,
    created_at   TIMESTAMPTZ    NOT NULL DEFAULT NOW(),
    updated_at   TIMESTAMPTZ    DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS idx_proyectos_semestre ON datos_proyectos (semestre);

-- ---------------------------------------------------------------
-- 2. Tabla de ponentes
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS datos_ponentes (
    id_ponent          BIGSERIAL    PRIMARY KEY,
    docente            VARCHAR(255) DEFAULT NULL,
    nombres            VARCHAR(120) NOT NULL,
    apellidos          VARCHAR(120) NOT NULL,
    cedula             VARCHAR(50)  DEFAULT NULL,
    telefono           VARCHAR(50)  DEFAULT NULL,
    semestre           SMALLINT     DEFAULT NULL,
    jornada            VARCHAR(50)  DEFAULT NULL,
    correo             VARCHAR(190) DEFAULT NULL,
    registration_token VARCHAR(64)  DEFAULT NULL,
    id_proyect         BIGINT       DEFAULT NULL
        REFERENCES datos_proyectos(id_proyect) ON DELETE SET NULL ON UPDATE CASCADE,
    created_at         TIMESTAMPTZ  NOT NULL DEFAULT NOW(),
    updated_at         TIMESTAMPTZ  DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS idx_ponentes_token      ON datos_ponentes (registration_token);
CREATE INDEX IF NOT EXISTS idx_ponentes_id_proyect ON datos_ponentes (id_proyect);

-- ---------------------------------------------------------------
-- 3. Funcion: actualizar updated_at automaticamente
-- ---------------------------------------------------------------
CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER LANGUAGE plpgsql AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$;

DROP TRIGGER IF EXISTS trg_proyectos_updated_at ON datos_proyectos;
CREATE TRIGGER trg_proyectos_updated_at
    BEFORE UPDATE ON datos_proyectos
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

DROP TRIGGER IF EXISTS trg_ponentes_updated_at ON datos_ponentes;
CREATE TRIGGER trg_ponentes_updated_at
    BEFORE UPDATE ON datos_ponentes
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

-- ---------------------------------------------------------------
-- 4. Trigger: evitar reasignacion de ponente a otro proyecto
-- ---------------------------------------------------------------
CREATE OR REPLACE FUNCTION prevent_ponente_reassign()
RETURNS TRIGGER LANGUAGE plpgsql AS $$
BEGIN
    IF OLD.id_proyect IS NOT NULL
       AND (NEW.id_proyect IS NULL OR NEW.id_proyect <> OLD.id_proyect) THEN
        RAISE EXCEPTION 'No se puede reasignar el proyecto de un ponente una vez asignado.';
    END IF;
    RETURN NEW;
END;
$$;

DROP TRIGGER IF EXISTS trg_prevent_ponente_reassign ON datos_ponentes;
CREATE TRIGGER trg_prevent_ponente_reassign
    BEFORE UPDATE ON datos_ponentes
    FOR EACH ROW EXECUTE FUNCTION prevent_ponente_reassign();