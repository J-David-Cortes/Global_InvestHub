export interface ConexionBroker {
    id: number;
    botId: number;
    botNombre: string;
    botPlataforma: string;
    brokerId: number;
    brokerNombre: string;
    apiKey: string;
    activo: boolean;
    fechaActivacion: string;
}