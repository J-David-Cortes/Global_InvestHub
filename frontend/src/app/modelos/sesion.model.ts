export interface Sesion {
  dispositivo: string;   // Ej: "Chrome — macOS"
  ubicacion: string;     // Ej: "New York, US"
  tiempo: string;        // Ej: "Active now" o "2 hours ago"
  esActual: boolean;     // true = es la sesión desde la que estás conectado ahora mismo
}