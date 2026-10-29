import { HttpErrorResponse, HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { catchError, throwError } from 'rxjs';
import { Auth, esRutaAppValida } from './auth';

// login y registro hoy SIEMPRE devuelven HTTP 200 (ver
// Backend/controladores/usuario.php: ponen http_response_code(200) para
// toda accion, y las dos quedan fuera de exigirUsuarioAutenticado()), asi
// que nunca deberian caer en la rama de 401 de abajo. Este chequeo es
// defensivo: si eso cambiara, un 401 en login/registro significa
// "credenciales invalidas", no "sesion vencida", y no debe desloguear a
// nadie (ni siquiera habria sesion que desloguear, en la mayoria de los
// casos).
function esPeticionDeLoginORegistro(url: string): boolean {
  try {
    const control = new URL(url, window.location.origin).searchParams.get('control');
    return control === 'login' || control === 'registro';
  } catch {
    return false;
  }
}

// Agrega el token a toda peticion saliente que tenga sesion activa (si no
// hay token, req sigue sin tocar). Un 401 del backend significa siempre
// "sesion invalida" (falta, mal formado, firma no coincide o expiro: ver
// auth_middleware.php) -- se centraliza aqui el deslogueo + redirect para
// no repetirlo en cada componente. Un 403 (rol insuficiente) NO desloguea:
// la sesion sigue siendo valida, solo falta permiso, asi que se deja
// propagar para que quien llamo lo maneje si le importa.
export const authInterceptor: HttpInterceptorFn = (req, next) => {
  const auth = inject(Auth);
  const router = inject(Router);
  const token = auth.token();
  const reqConToken = token
    ? req.clone({ setHeaders: { Authorization: `Bearer ${token}` } })
    : req;

  return next(reqConToken).pipe(
    catchError((error: HttpErrorResponse) => {
      const habiaToken = auth.token() !== null;
      if (error.status === 401 && habiaToken && !esPeticionDeLoginORegistro(req.url)) {
        const urlActual = router.url;
        auth.cerrarPorSesionInvalida(esRutaAppValida(urlActual) ? urlActual : undefined);
      }
      return throwError(() => error);
    })
  );
};
