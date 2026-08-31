// Este archivo define la "forma" que debe tener cada engine (bot activo)
// que el usuario ya tiene corriendo, a diferencia del Marketplace que
// muestra bots disponibles para SUSCRIBIRSE.

export interface Engine {
    id: number;
    nombre: string;              // Ej: "Quantum FX Alpha"
    version: string;             // Ej: "v2.4.1"
    plataforma: PlataformaEngine;
    estado: EstadoEngine;
    ganancia: string;            // Ej: "+$4,230.50" (ya formateado, como en Figma)
    gananciaPositiva: boolean;   // true = ganancia (cyan), false = pérdida (gold)
    numeroOperaciones: number;
    winRate: string;             // Ej: "71.3%"
    tiempoActivo: string;        // Ej: "12d 4h" o "—" si está detenido
    par: string;                 // Ej: "EUR/USD"
    fechaInicio: string;
    equity: string;
}

export type PlataformaEngine = 'MT5' | 'LEAN' | 'cTrader';
export type EstadoEngine = 'running' | 'paused' | 'stopped';