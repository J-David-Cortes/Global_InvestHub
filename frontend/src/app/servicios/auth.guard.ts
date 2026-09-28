import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { Auth } from './auth';

// Protege /app completo (un solo canActivate en la ruta padre, no en cada
// hijo). Sin sesion, redirige a /login.
export const authGuard: CanActivateFn = () => {
  const auth = inject(Auth);
  const router = inject(Router);

  if (auth.estaAutenticado()) return true;

  router.navigateByUrl('/login');
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
