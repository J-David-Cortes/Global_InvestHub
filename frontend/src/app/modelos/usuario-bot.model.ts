// Respuesta de usuario_bot.php?control=estadoBroker: base para decidir el
// flujo de Subscribe (primera vez vs. sugerir el broker que ya usa).
export interface EstadoBroker {
  tieneBroker: boolean;
  ultimoBrokerId: number | null;
}

// Respuesta de las acciones de escritura del backend. Responde HTTP 200
// aun cuando rechaza la accion (limite del plan, duplicado...), asi que
// quien llama debe revisar Resultado en el next, no en el error.
export interface RespuestaEscritura {
  Resultado: 'OK' | 'Error';
  mensaje: string;
}
