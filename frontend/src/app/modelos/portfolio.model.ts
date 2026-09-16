export interface ResumenPortfolio {
    totalEquity: number;
    totalPnl: number;
    pnlHoy: number;
    pnlMes: number;
    pnlAnio: number;
}

export interface PosicionAbierta {
    id: number;
    botId: number;
    botNombre: string;
    precioEntrada: number;
    precioActual: number | null;
    montoInvertido: number;
    fechaApertura: string;
}

export interface PosicionCerrada {
    id: number;
    botId: number;
    botNombre: string;
    precioEntrada: number;
    precioCierre: number;
    montoInvertido: number;
    resultadoPnl: number;
    montoRetornado: number;
    fechaApertura: string;
    fechaCierre: string;
}