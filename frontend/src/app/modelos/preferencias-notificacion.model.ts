// Las 6 preferencias, agrupadas en un solo objeto
export interface PreferenciasNotificacion {
    emailTrades: boolean;
    emailAlerts: boolean;
    emailReports: boolean;
    pushTrades: boolean;
    pushAlerts: boolean;
    pushNews: boolean;
}

// Esto nos permite decir "dame cualquiera de las 6 llaves válidas de PreferenciasNotificacion"
export type ClavePreferencia = keyof PreferenciasNotificacion;

// La forma de cada fila individual dentro de una tarjeta (Email o Push)
export interface ItemNotificacion {
    etiqueta: string;
    descripcion: string;
    clave: ClavePreferencia;
}