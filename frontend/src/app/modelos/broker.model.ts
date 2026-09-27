// Opcion del <select> de brokers. broker.php devuelve id_broker como string;
// el componente lo convierte a number al armar las opciones.
export interface BrokerOpcion {
  id: number;
  nombre: string;
}
