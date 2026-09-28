import { HttpErrorResponse, HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { catchError, throwError } from 'rxjs';
import { Auth } from './auth';

// Agrega el token a toda peticion saliente que tenga sesion activa (si no
// hay token, req sigue sin tocar). Un 401 del backend significa siempre
// "sesion invalida" (falta, mal formado, firma no coincide o expiro: ver
// auth_middleware.php) -- se centraliza aqui el deslogueo + redirect para
// no repetirlo en cada componente. Un 403 (rol insuficiente) NO desloguea:
// la sesion sigue siendo valida, solo falta permiso, asi que se deja
// propagar para que quien llamo lo maneje si le importa.
export const authInterceptor: HttpInterceptorFn = (req, next) => {
  const auth = inject(Auth);
  const token = auth.token();
  const reqConToken = token
    ? req.clone({ setHeaders: { Authorization: `Bearer ${token}` } })
    : req;

  return next(reqConToken).pipe(
    catchError((error: HttpErrorResponse) => {
      if (error.status === 401) {
        auth.logout();
      }
      return throwError(() => error);
    })
  );
};
