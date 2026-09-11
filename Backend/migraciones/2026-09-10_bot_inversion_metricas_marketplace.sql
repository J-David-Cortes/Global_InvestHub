-- Migración: columnas simples pendientes de la primera ronda de análisis
-- (comparación bot_inversion real vs. mock de Marketplace / Algoritmo.model.ts).
--
-- Estas 8 columnas son datos de un solo valor por bot (texto, número o
-- booleano) — no requieren tabla aparte ni relación con otras tablas,
-- a diferencia de tags/historialRendimiento/rating que quedaron pendientes
-- de una decisión de diseño distinta (JSON vs tabla nueva) y NO están
-- incluidas aquí.
--
-- Aún no ejecutado. Pendiente de revisión.

ALTER TABLE bot_inversion
  ADD COLUMN subtitulo VARCHAR(150) NULL AFTER precio_individual,
  -- ENUM: MySQL valida en la propia BD que solo se guarden estos valores
  -- exactos (igual que el type CategoriaAlgoritmo en el frontend) — si alguien
  -- intenta insertar una categoría que no está en la lista, MySQL rechaza el INSERT.
  ADD COLUMN categoria ENUM('Forex','Crypto','Equities','Commodities','Options') NULL AFTER subtitulo,
  ADD COLUMN plataforma ENUM('MT5','LEAN','cTrader') NULL AFTER categoria,
  -- verificado es un booleano real (no un dato "opcional" como los de arriba):
  -- un bot siempre ES o NO ES verificado, nunca "se desconoce" -> NOT NULL + DEFAULT 0.
  ADD COLUMN verificado TINYINT(1) NOT NULL DEFAULT 0 AFTER plataforma,
  ADD COLUMN rendimiento_anual DECIMAL(6,2) NULL AFTER verificado,
  ADD COLUMN sharpe_ratio DECIMAL(4,2) NULL AFTER rendimiento_anual,
  ADD COLUMN max_drawdown DECIMAL(5,2) NULL AFTER sharpe_ratio,
  ADD COLUMN win_rate DECIMAL(5,2) NULL AFTER max_drawdown;

-- Nota: subtitulo, categoria, plataforma y las 4 métricas quedan NULL para
-- filas existentes (ej. el bot de prueba "FIRSBOT") porque son datos
-- descriptivos/de desempeño que aún no se conocen para ese registro —
-- distinto de "verificado", que sí necesita un valor definido siempre.
