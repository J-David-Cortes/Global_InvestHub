-- Migración: planes reales (Starter/Pro/Enterprise) + control de activación
-- de bots por usuario + flag de bots VIP con precio individual.
--
-- Contexto del modelo de negocio:
--   - Starter (gratis): puede ver el Marketplace pero no usar bots (limite_bots = 0).
--   - Pro: puede usar hasta 3 bots simultáneos que no sean VIP (limite_bots = 3).
--   - Enterprise: puede usar todos los bots que no sean VIP (limite_bots = NULL = ilimitado).
--   - Bots VIP: se pagan aparte del plan, sin importar si el usuario es Enterprise.
--   - El admin desactiva manualmente el acceso de un usuario a un bot
--     (ej. al cancelar su suscripción) sin borrar el historial de la conexión.
--
-- Ejecutado manualmente contra marketplace_2 el 2026-09-10.

-- 1) suscripcion: límite de bots por plan
ALTER TABLE suscripcion
  ADD COLUMN limite_bots INT NULL AFTER precio;

-- Reemplazo de las 3 filas de prueba (Platino/Plus/Pro Plus) por los planes reales.
-- Se usa UPDATE y no DELETE+INSERT porque usuario_suscripcion estaba vacía
-- al momento de la migración (sin riesgo de romper referencias existentes).
UPDATE suscripcion SET nombre = 'Starter',    precio = 0.00,   limite_bots = 0    WHERE id_suscripcion = 2; -- antes: Platino $30
UPDATE suscripcion SET nombre = 'Pro',        precio = 79.00,  limite_bots = 3    WHERE id_suscripcion = 3; -- antes: Plus $50
UPDATE suscripcion SET nombre = 'Enterprise', precio = 299.00, limite_bots = NULL WHERE id_suscripcion = 4; -- antes: Pro Plus $100

-- 2) usuario_bot: activar/desactivar acceso sin borrar la fila, y registrar
-- si el acceso a ese bot fue pagado individualmente (VIP).
ALTER TABLE usuario_bot
  ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1 AFTER fo_broker;
ALTER TABLE usuario_bot
  ADD COLUMN fecha_activacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER activo;
ALTER TABLE usuario_bot
  ADD COLUMN fecha_desactivacion DATETIME NULL AFTER fecha_activacion;
ALTER TABLE usuario_bot
  ADD COLUMN fo_pasarela INT NULL AFTER fecha_desactivacion;
ALTER TABLE usuario_bot
  ADD CONSTRAINT usuario_bot_ibfk_4
  FOREIGN KEY (fo_pasarela) REFERENCES pasarela_pagos(id_pasarela) ON UPDATE CASCADE;

-- 3) bot_inversion: marcar bots VIP y su precio individual
ALTER TABLE bot_inversion
  ADD COLUMN es_vip TINYINT(1) NOT NULL DEFAULT 0 AFTER algoritmo;
ALTER TABLE bot_inversion
  ADD COLUMN precio_individual DECIMAL(10,2) NULL AFTER es_vip;
