// Este archivo define la "forma" que debe tener cada bot/algoritmo
// que mostramos en el Marketplace. Es como una plantilla o molde.

export interface Algoritmo {
    id: number;
    nombre: string;
    subtitulo: string;
    descripcion: string;
    categoria: CategoriaAlgoritmo;
    plataforma: PlataformaAlgoritmo;
    verificado: boolean;

    rendimientoAnual: number;
    sharpeRatio: number;
    maxDrawdown: number;
    winRate: number;

    desarrollador: string;
    rating: number;
    numeroReviews: number;
    numeroSuscriptores: number;

    tags: string[];

    precioMensual: number;

    historialRendimiento: number[];
}

export type CategoriaAlgoritmo =
    | 'Forex'
    | 'Crypto'
    | 'Equities'
    | 'Commodities'
    | 'Options';

export type PlataformaAlgoritmo = 'MT5' | 'LEAN' | 'cTrader';