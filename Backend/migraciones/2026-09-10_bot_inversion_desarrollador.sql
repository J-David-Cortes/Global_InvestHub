-- Migración (Ronda 3): columna "desarrollador" en bot_inversion.
--
-- Decisión ya tomada: es texto libre (ej. "AlgoQuant Labs"), no una
-- llave foránea hacia la tabla usuario. bot_inversion sigue sin ningún
-- fo_usuario -> un bot NO pertenece a un usuario real de la plataforma,
-- solo muestra el nombre de quien lo desarrolló como dato informativo
-- en la tarjeta del Marketplace.
--
-- Si en el futuro se decide que un usuario real de la plataforma pueda
-- publicar y ser dueño de un bot, esta columna de texto se reemplazaría
-- por un fo_usuario (FK a usuario) — pero esa es una decisión aparte,
-- no incluida en esta migración.
--
-- Aún no ejecutado. Pendiente de revisión.

ALTER TABLE bot_inversion
  ADD COLUMN desarrollador VARCHAR(100) NULL AFTER win_rate;
