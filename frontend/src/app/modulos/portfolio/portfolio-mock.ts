import { Holding, PosicionCerrada, MetricaPortfolio } from '../../modelos/portfolio.model';

export const METRICAS_MOCK: MetricaPortfolio[] = [
    { etiqueta: 'TOTAL EQUITY', valor: '$124,560', cambio: '+47.2% YTD', esPositiva: true },
    { etiqueta: 'TODAY P&L', valor: '+$1,247.80', cambio: '+1.01% hoy', esPositiva: true },
    { etiqueta: 'MTD P&L', valor: '+$12,540', cambio: '+11.2% este mes', esPositiva: true },
    { etiqueta: 'YTD P&L', valor: '+$47,230', cambio: 'desde Ene 2026', esPositiva: true },
];

// Un color por cada holding, para pintar la barra de distribución y sus puntos
export const COLORES_ASIGNACION: string[] = [
    '#00d4c8', '#20c997', '#f0b429', '#818cf8', '#f87171', '#374151',
];

export const HOLDINGS_MOCK: Holding[] = [
    { nombre: 'Quantum FX Alpha', algoritmo: 'EUR/USD', tamano: '$28,230', entrada: '1.0812', actual: '1.0847', ganancia: '+$4,230.50', gananciaPct: '+17.6%', esPositiva: true, asignacionPct: 23 },
    { nombre: 'Neural Scalper X', algoritmo: 'EUR/USD', tamano: '$18,912', entrada: '1.0795', actual: '1.0847', ganancia: '+$8,912.30', gananciaPct: '+47.1%', esPositiva: true, asignacionPct: 15 },
    { nombre: 'Equity Trend v2', algoritmo: 'SPY/QQQ', tamano: '$17,100', entrada: '520.40', actual: '528.30', ganancia: '+$2,100.80', gananciaPct: '+14.0%', esPositiva: true, asignacionPct: 14 },
    { nombre: 'Crypto Momentum Pro', algoritmo: 'BTC/ETH', tamano: '$11,890', entrada: '64,200', actual: '67,234', ganancia: '+$1,890.20', gananciaPct: '+18.9%', esPositiva: true, asignacionPct: 10 },
    { nombre: 'Gold Hedge Beta', algoritmo: 'XAU/USD', tamano: '$9,880', entrada: '2,390', actual: '2,341', ganancia: '-$120.00', gananciaPct: '-1.2%', esPositiva: false, asignacionPct: 8 },
    { nombre: 'Cash Reserve', algoritmo: 'USD', tamano: '$38,547', entrada: '—', actual: '—', ganancia: '$0.00', gananciaPct: '0%', esPositiva: true, asignacionPct: 30 },
];

export const POSICIONES_CERRADAS_MOCK: PosicionCerrada[] = [
    { nombre: 'Volatility Arb', fechaCierre: 'Aug 18', invertido: '$40,000', retornado: '$47,200', ganancia: '+$7,200', retornoPct: '+18.0%', esPositiva: true },
    { nombre: 'FX Carry Trade', fechaCierre: 'Aug 14', invertido: '$15,000', retornado: '$16,840', ganancia: '+$1,840', retornoPct: '+12.3%', esPositiva: true },
    { nombre: 'Mean Rev Bonds', fechaCierre: 'Aug 10', invertido: '$20,000', retornado: '$19,200', ganancia: '-$800', retornoPct: '-4.0%', esPositiva: false },
    { nombre: 'Quantum FX v1', fechaCierre: 'Aug 3', invertido: '$25,000', retornado: '$29,800', ganancia: '+$4,800', retornoPct: '+19.2%', esPositiva: true },
];