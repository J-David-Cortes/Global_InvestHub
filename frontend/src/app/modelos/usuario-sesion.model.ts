// Snapshot minimo del usuario guardado en localStorage tras el login.
// Puede quedar desactualizado si el perfil o el rol cambian en la BD
// (se refresca recien en el proximo login) -- es solo para mostrar en
// pantalla (ej. nombre en la barra lateral), nunca para decisiones de
// autorizacion: esas siempre las valida el backend con datos frescos.
export interface UsuarioSesion {
  id: number;
  nombre: string;
  email: string;
  fo_permiso: number;
}

// Respuesta de usuario.php?control=login.
export interface RespuestaLogin {
  Resultado: 'OK' | 'Error';
  mensaje?: string;
  token?: string;
  usuario?: UsuarioSesion;
}
