export interface PlanCatalogo {
    id: number;
    nombre: string;
    precio: number;
    limiteBots: number | null; // null = ilimitado (Enterprise)
}

export interface PlanActual extends PlanCatalogo {
    fechaInicio: string | null;
    fechaFin: string | null;
    suscripcionActiva: boolean;
}

// Copy de marketing por plan (no viene de la BD, es texto de interfaz).
// La clave debe coincidir con el "nombre" real de cada plan en la BD.
export const CARACTERISTICAS_PLAN: Record<string, string[]> = {
    Starter: ['Marketplace básico', 'Soporte por email'],
    Trial: ['Prueba gratuita por 3 meses', '1 bot básico incluido', 'Soporte por email'],
    Pro: ['Todo el Marketplace', 'Analytics avanzado', 'Soporte prioritario'],
    Enterprise: ['API dedicada', 'SLA garantizado', 'Gestor de cuenta', 'Infraestructura dedicada'],
};

// Genera la primera viñeta dinámicamente a partir del límite real de bots.
export function viñetaLimiteBots(limiteBots: number | null): string {
    if (limiteBots === null) return 'Bots ilimitados (excepto VIP)';
    if (limiteBots === 0) return 'Solo Marketplace, sin bots';
    return `Hasta ${limiteBots} bots activos`;
}