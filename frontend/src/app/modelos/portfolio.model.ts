// Define la "forma" de una posición ABIERTA (holding actual)
export interface Holding {
    nombre: string;
    algoritmo: string;         // Ej: "EUR/USD" (el par/activo que opera)
    tamano: string;            // Ej: "$28,230" (ya formateado)
    entrada: string;           // Ej: "1.0812" o "—" si no aplica (como Cash Reserve)
    actual: string;
    ganancia: string;          // Ej: "+$4,230.50"
    gananciaPct: string;       // Ej: "+17.6%"
    esPositiva: boolean;
    asignacionPct: number;     // Ej: 23 (para la barra de distribución, en %)
}

// Define la "forma" de una posición YA CERRADA (historial)
export interface PosicionCerrada {
    nombre: string;
    fechaCierre: string;       // Ej: "Aug 18"
    invertido: string;
    retornado: string;
    ganancia: string;
    retornoPct: string;
    esPositiva: boolean;
}

// Las 4 tarjetas de métricas superiores
export interface MetricaPortfolio {
    etiqueta: string;
    valor: string;
    cambio: string;
    esPositiva: boolean;
}