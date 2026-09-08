export interface ApiKey {
    id: number;
    nombre: string;
    plataforma: 'MT5' | 'LEAN' | 'API';
    fechaCreacion: string;
    ultimoUso: string;
    claveOculta: string; // Ej: "gih_mt5_sk_...a4f2"
}