-- Migración (Ronda 4): rating y numero_reviews en bot_inversion.
--
-- Decisión ya tomada: por ahora NO se construye un sistema de reseñas
-- real (eso implicaría una tabla nueva tipo resena_bot con usuario+bot+
-- puntuación+comentario, para poder saber quién calificó y evitar que
-- un mismo usuario vote varias veces). Se guardan como 2 columnas fijas
-- en bot_inversion, igual que rendimiento_anual/sharpe_ratio/etc:
-- un valor editado manualmente (o precargado), no calculado a partir
-- de votos individuales.
--
-- Si más adelante se construye el sistema de reseñas real, estas 2
-- columnas podrían pasar a calcularse con AVG()/COUNT() sobre esa
-- tabla nueva en vez de editarse a mano -- pero esa es una migración
-- aparte, no incluida aquí.
--
-- Aún no ejecutado. Pendiente de revisión.

ALTER TABLE bot_inversion
  ADD COLUMN rating DECIMAL(2,1) NULL AFTER desarrollador,
  ADD COLUMN numero_reviews INT NOT NULL DEFAULT 0 AFTER rating;
