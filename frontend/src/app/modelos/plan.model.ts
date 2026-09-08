export interface Plan {
    nombre: string;
    precio: number;       // 0 = gratis
    descripcion: string;
    caracteristicas: string[];
    esActual: boolean;
}

export interface Pago {
    fecha: string;
    descripcion: string;
    monto: string;
    estado: string;
}