-- Migracion: agregar plan "Trial" a la tabla suscripcion.
--
-- Contexto: el estado_suscripcion.id_estado=4 ("PRUEBA") es dato legado
-- sin ninguna referencia real en el codigo (verificado por grep en todo
-- Backend/). Un trial real NO se representa con ese estado -- se
-- representa como una fila normal en usuario_suscripcion con
-- fo_suscripcion apuntando a este plan "Trial" y fo_estado = 1 (Activo),
-- igual que cualquier otro plan. El criterio de "suscripcion activa hoy"
-- sigue siendo unicamente: fo_estado = 1 AND fecha_inicio <= CURDATE()
-- AND fecha_fin >= CURDATE() -- no se agrega ningun caso especial para
-- el trial.
--
-- TODO (pendiente, fuera de alcance de esta migracion): el vencimiento
-- automatico del trial a los 3 meses con cobro automatico del plan
-- basico requiere una tarea programada (cron job, no existe todavia) mas
-- una pasarela de pagos real conectada para cobrar sin intervencion
-- humana. Por ahora, un trial simplemente deja de contar como "activo"
-- cuando se cumple su fecha_fin (por el criterio de fechas ya
-- implementado), pero nadie lo convierte automaticamente a un plan de
-- pago todavia -- eso se resuelve cuando se trabaje en pagos/facturacion.

INSERT INTO suscripcion (nombre, precio, limite_bots)
VALUES ('Trial', 0.00, 1);
