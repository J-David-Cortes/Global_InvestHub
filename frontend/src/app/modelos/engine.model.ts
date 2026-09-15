// Estado simplificado: por ahora solo distinguimos "tiene acceso activo"
// o no -- la base de datos real no guarda un tercer estado "paused"
// todavía (ver TODO en engines.ts).
export type EstadoEngine = 'running' | 'stopped';

export interface Engine {
    id: number;              // id_conexion (la fila de usuario_bot)
    botId: number;
    nombre: string;           // viene de botNombre
    plataforma: string;       // viene de botPlataforma
    categoria: string;        // viene de botCategoria
    brokerNombre: string;
    activo: boolean;          // dato real de la BD
    fechaActivacion: string;
    fechaDesactivacion: string | null;

    // Campos SIN datos reales todavía (no existe motor de trading en vivo
    // conectado). Se muestran como placeholder "—" en el HTML hasta que
    // exista una tabla de operaciones/resultados real. Ver TODO en
    // engines.html.
    version?: string;
    par?: string;
    ganancia?: string;
    gananciaPositiva?: boolean;
    numeroOperaciones?: number;
    winRate?: string;
    equity?: string;
}