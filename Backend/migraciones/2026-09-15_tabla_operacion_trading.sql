-- Migracion: tabla nueva operacion_trading, para respaldar Portfolio
-- (Open/Closed Positions) con datos reales en vez de mock.
--
-- Contexto: Portfolio hoy no tiene NINGUN dato real detras (ni Equity,
-- ni Asset Allocation, ni operaciones abiertas/cerradas) -- se
-- diagnostico contra las 17 tablas del esquema real y ninguna respalda
-- estos conceptos. Esta tabla es el minimo necesario para empezar,
-- pensada para ampliarse cuando exista el motor de trading real en
-- Python que la vaya a poblar. Empieza vacia (0 filas).
--
-- Decisiones de diseño (acordadas antes de escribir esta migracion):
--   1. NO hay columna "tipo" (abierta/cerrada). Se deriva de
--      fecha_cierre: NULL = abierta, con fecha = cerrada. Evita guardar
--      un dato redundante que se podria desincronizar con fecha_cierre
--      (mismo criterio ya aplicado con numeroSuscriptores en
--      bot_inversion, calculado con COUNT en vez de columna aparte).
--   2. precio_actual y precio_cierre son columnas SEPARADAS (no una
--      sola compartida): representan conceptos distintos -- un valor
--      vivo que cambia mientras la posicion sigue abierta, vs. un valor
--      final fijo al cerrar. Ambas NULL cuando no aplican.
--   3. fo_usuario + fo_bot son llaves foraneas DIRECTAS (no a traves de
--      usuario_bot.id_conexion). Es menos normalizado, pero mas simple
--      de consultar -- decision consciente dado que esta tabla se va a
--      ampliar de todas formas cuando exista el motor de trading real.
--   4. precio_entrada/precio_actual/precio_cierre usan DECIMAL(15,5)
--      (no DECIMAL(10,2) como el dinero) porque bot_inversion.categoria
--      incluye Forex, donde el precio de un par necesita 4-5 decimales.
--   5. resultado_pnl es DECIMAL normal (no UNSIGNED) porque puede ser
--      negativo (perdida) -- mismo estilo que el resto del esquema, que
--      no usa UNSIGNED en ninguna columna de dinero.
--
-- TODO: cuando exista el motor de trading real, esta tabla
-- probablemente necesite mas columnas (ej. simbolo/instrumento
-- operado, direccion long/short, broker de ejecucion, fees) -- se
-- amplia en una migracion aparte cuando llegue ese momento, no ahora.

CREATE TABLE `operacion_trading` (
  `id_operacion` int(11) NOT NULL AUTO_INCREMENT,
  `fo_usuario` int(11) NOT NULL,
  `fo_bot` int(11) NOT NULL,
  `precio_entrada` decimal(15,5) NOT NULL,
  `precio_actual` decimal(15,5) DEFAULT NULL,
  `precio_cierre` decimal(15,5) DEFAULT NULL,
  `monto_invertido` decimal(10,2) NOT NULL,
  `fecha_apertura` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_cierre` datetime DEFAULT NULL,
  `resultado_pnl` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_operacion`),
  KEY `fo_usuario` (`fo_usuario`),
  KEY `fo_bot` (`fo_bot`),
  CONSTRAINT `operacion_trading_ibfk_1` FOREIGN KEY (`fo_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `operacion_trading_ibfk_2` FOREIGN KEY (`fo_bot`) REFERENCES `bot_inversion` (`id_bot`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
