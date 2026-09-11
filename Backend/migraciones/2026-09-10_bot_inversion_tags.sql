-- Migración (Ronda 5): columna "tags" en bot_inversion, tipo JSON.
--
-- Decisión ya tomada: en vez de crear una tabla nueva tipo bot_tag
-- (fo_bot + tag, una fila por etiqueta), se guarda el array completo
-- de strings directamente en una sola columna, ej: ["SCALPING","HIGH-FREQ"].
-- Es el camino más simple para mostrar las etiquetas en la tarjeta del
-- Marketplace, aunque sacrifica la posibilidad de hacer consultas SQL
-- tipo "todos los bots con el tag CRYPTO" de forma eficiente (con JSON
-- se puede, pero es más complicado que un simple WHERE en una tabla
-- normalizada). Si en el futuro se necesita filtrar/buscar por tag de
-- forma pesada, ahí sí valdría la pena migrar a una tabla aparte.
--
-- MySQL valida que el contenido de esta columna sea JSON válido en cada
-- INSERT/UPDATE (rechaza texto que no tenga la forma de un array/objeto JSON).
--
-- Aún no ejecutado. Pendiente de revisión.

ALTER TABLE bot_inversion
  ADD COLUMN tags JSON NULL AFTER numero_reviews;
