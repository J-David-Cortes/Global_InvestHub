import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { Auth, esRutaAppValida } from './auth';

// Protege /app completo (un solo canActivate en la ruta padre, no en cada
// hijo). Sin sesion (nunca hubo token): a login, con returnUrl para volver
// aqui despues. Con un token guardado pero ya vencido: tambien a login,
// pero via cerrarPorSesionInvalida() para limpiar el token muerto y
// mostrar el aviso de "sesion expirada".
export const authGuard: CanActivateFn = (route, state) => {
  const auth = inject(Auth);
  const router = inject(Router);

  if (auth.estaAutenticado()) return true;

  if (auth.token() !== null) {
    auth.cerrarPorSesionInvalida(state.url);
  } else {
    const queryParams = esRutaAppValida(state.url) ? { returnUrl: state.url } : {};
    router.navigate(['/login'], { queryParams });
  }
  return false;
};

// Guard inverso: si ya hay sesion activa, /login redirige a /app en vez de
// mostrar el formulario de nuevo.
export const soloInvitadoGuard: CanActivateFn = () => {
  const auth = inject(Auth);
  const router = inject(Router);

  if (!auth.estaAutenticado()) return true;

  router.navigateByUrl('/app');
  return false;
};
