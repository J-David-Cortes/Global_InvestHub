-- Migración (Ronda 6): columna "historial_rendimiento" en bot_inversion,
-- tipo JSON (mismo patrón que "tags" en la Ronda 5).
--
-- Decisión ya tomada: en vez de una tabla nueva tipo bot_historial
-- (fo_bot + fecha/periodo + valor, una fila por punto del gráfico), se
-- guarda el array completo de números en una sola columna, ej:
-- [100, 104, 102, 108, 112, 110, 118, 124, 121, 127].
-- Es solo para dibujar el sparkline SVG en la tarjeta del Marketplace,
-- no para hacer análisis histórico real (que sí necesitaría una tabla
-- aparte con fecha por punto, para poder filtrar por rango de fechas).
--
-- Nota: en MariaDB (la versión que usa este proyecto, ver Ronda 5) el
-- tipo JSON se implementa como LONGTEXT + CHECK(json_valid(...)) — mismo
-- comportamiento de validación, solo cambia la etiqueta interna.
--
-- Aún no ejecutado. Pendiente de revisión.

ALTER TABLE bot_inversion
  ADD COLUMN historial_rendimiento JSON NULL AFTER tags;
