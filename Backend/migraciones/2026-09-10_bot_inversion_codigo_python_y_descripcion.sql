-- Migración (Ronda 7): renombrar "algoritmo" -> "codigo_python" y ampliar
-- su tipo, + agregar "descripcion" como columna nueva separada.
--
-- Corrección de diseño: la columna "algoritmo" NO era una descripción de
-- texto corto (como se asumió en rondas anteriores) -- ahí se va a guardar
-- el CÓDIGO FUENTE PYTHON real del bot. VARCHAR(1000) se queda corto para
-- código; se cambia a LONGTEXT.
--
-- *** SEGURIDAD: codigo_python es sensible. NO debe exponerse jamás en el
-- *** JSON de respuesta de ningún endpoint público (ej. consulta() del
-- *** Marketplace). Los usuarios solo deben ver metricas/descripcion, nunca
-- *** el código del bot. Esto se aplicará en el modelo PHP en un paso
-- *** aparte (fuera de esta migración) -- pendiente, marcado explícitamente
-- *** aquí para no olvidarlo.
--
-- CHANGE COLUMN (no DROP+ADD) preserva los datos existentes: el bot de
-- prueba FIRSBOT conserva su valor actual ("POR DEFINIR") bajo el nuevo
-- nombre de columna, sin perder nada.
--
-- "descripcion" es la columna nueva y separada: el texto corto que SÍ se
-- muestra en la tarjeta del Marketplace (ej. "High-frequency EUR/USD
-- scalping..."). Esta sí es segura de exponer en la API pública.
--
-- Aún no ejecutado. Pendiente de revisión.

ALTER TABLE bot_inversion
  CHANGE COLUMN algoritmo codigo_python LONGTEXT NOT NULL;

ALTER TABLE bot_inversion
  ADD COLUMN descripcion VARCHAR(500) NULL AFTER subtitulo;
