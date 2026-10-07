-- Migracion: tabla usuario_aceptacion.
--
-- Contexto: el registro publico pide aceptar terminos, aviso de riesgo y,
-- opcionalmente, comunicaciones comerciales. Hay que guardar que se acepto,
-- que version del texto legal y cuando.
--
-- Decisiones:
--   1. Una fila por aceptacion. 'comercial' solo se inserta si el usuario la
--      marca; la ausencia de fila significa que no la acepto.
--   2. version identifica el texto legal aceptado (por ejemplo '2026-10-01').
--      La restriccion UNIQUE evita duplicar la misma aceptacion.
--   3. Sin IP ni datos extra, como se acordo.
--   4. ON DELETE CASCADE: al borrar un usuario se borran sus aceptaciones.
--
-- Aplicar:
CREATE TABLE usuario_aceptacion (
  id_aceptacion INT(11) NOT NULL AUTO_INCREMENT,
  fo_usuario    INT(11) NOT NULL,
  tipo          ENUM('terminos','riesgo','comercial') NOT NULL,
  version       VARCHAR(20) NOT NULL,
  aceptado_en   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_aceptacion),
  UNIQUE KEY uq_aceptacion_usuario_tipo_version (fo_usuario, tipo, version),
  KEY fo_usuario (fo_usuario),
  CONSTRAINT usuario_aceptacion_ibfk_1 FOREIGN KEY (fo_usuario)
    REFERENCES usuario (id_usuario) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Revertir:
-- DROP TABLE usuario_aceptacion;
