-- Migracion: tabla nueva preferencia_notificacion, para respaldar la
-- pestana Notifications de Settings con datos reales en vez de mock.
--
-- Contexto: el frontend (PreferenciasNotificacion) maneja 6 preferencias
-- booleanas fijas en 2 categorias:
--   Email: emailTrades, emailAlerts, emailReports
--   Push:  pushTrades,  pushAlerts,  pushNews
-- Hasta hoy no existia ninguna tabla que las guardara.
--
-- Decisiones de diseño (acordadas antes de escribir esta migracion):
--   1. Una FILA POR USUARIO con 6 columnas booleanas (no una fila por
--      preferencia). El conjunto de preferencias es fijo y esta escrito
--      en el frontend, asi que cada preferencia es una columna con tipo,
--      NOT NULL y DEFAULT: la BD valida por si sola (una clave escrita
--      con un typo en una tabla clave-valor crearia una preferencia
--      fantasma sin ningun error). Guardar las 6 es un solo UPDATE/upsert.
--      Si en el futuro las preferencias pasan a ser dinamicas (definidas
--      por el usuario, o una matriz canal x evento que crece), ese es el
--      momento de migrar a una tabla clave-valor, en una migracion aparte.
--   2. PRIMARY KEY = fo_usuario directamente, sin id_preferencia aparte:
--      la relacion con usuario es 1 a 1 (exactamente una fila por
--      usuario), asi que un identificador adicional no tendria proposito.
--      La PK tambien garantiza que no puedan existir dos filas del mismo
--      usuario.
--   3. Defaults iguales a los del mock del frontend: emailTrades,
--      emailAlerts, pushTrades y pushAlerts = 1 (activadas);
--      emailReports y pushNews = 0 (desactivadas).
--   4. ON DELETE CASCADE hacia usuario, igual que usuario_bot y
--      usuario_suscripcion: son preferencias personales que no tienen
--      sentido sin el usuario.
--   5. NO se inserta ninguna fila de datos. Un usuario sin fila (como el
--      usuario 2 hoy) se trata en el modelo PHP: devuelve los defaults
--      cuando no existe fila, y la crea en el primer guardado. Ojo: esos
--      mismos defaults quedan repetidos en el modelo para ese caso --
--      mantener ambos sitios sincronizados si alguna vez cambian.
--
-- Nombres de columna en snake_case (convencion del esquema); el modelo
-- los expone en camelCase con alias (email_trades AS emailTrades, etc.).
--
-- TODO: guardar las preferencias es real, pero hoy NO existe ningun
-- motor que envie emails ni notificaciones push, asi que nada las
-- consume todavia. Se conectan cuando exista ese servicio.

CREATE TABLE `preferencia_notificacion` (
  `fo_usuario` int(11) NOT NULL,
  `email_trades` tinyint(1) NOT NULL DEFAULT 1,
  `email_alerts` tinyint(1) NOT NULL DEFAULT 1,
  `email_reports` tinyint(1) NOT NULL DEFAULT 0,
  `push_trades` tinyint(1) NOT NULL DEFAULT 1,
  `push_alerts` tinyint(1) NOT NULL DEFAULT 1,
  `push_news` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`fo_usuario`),
  CONSTRAINT `preferencia_notificacion_ibfk_1` FOREIGN KEY (`fo_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
