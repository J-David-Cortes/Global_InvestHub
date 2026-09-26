export interface ResumenDashboard {
    totalEquity: number;
    totalPnl: number;
    pnlHoy: number;
    pnlMes: number;
    pnlAnio: number;
}

export interface SharpeDashboard {
    sharpeRatio: number | null;
    operacionesCerradas: number;
}

export interface PosicionActivaDashboard {
    id: number;
    botId: number;
    botNombre: string;
    precioEntrada: number;
    precioActual: number | null;
    montoInvertido: number;
    fechaApertura: string;
}