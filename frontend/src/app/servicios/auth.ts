import { HttpClient } from '@angular/common/http';
import { Injectable, inject, signal } from '@angular/core';
import { Router } from '@angular/router';
import { tap } from 'rxjs';
import { Usuario } from './usuario';
import { DatosRegistro, RespuestaLogin, UsuarioSesion } from '../modelos/usuario-sesion.model';

const CLAVE_TOKEN = 'auth_token';
const CLAVE_USUARIO = 'auth_usuario';

// Solo rutas que empiezan por '/app' pueden usarse como returnUrl: vienen
// de un query param de la URL (potencialmente manipulado por quien comparta
// el enlace), asi que nunca se navega a una ruta externa ni a algo como
// '//evil.com' -- eso tampoco empieza por '/app'.
export function esRutaAppValida(ruta: string | null | undefined): ruta is string {
  return !!ruta && (ruta === '/app' || ruta.startsWith('/app/'));
}

@Injectable({
  providedIn: 'root',
})
export class Auth {
  private usuarioService = inject(Usuario);
  private router = inject(Router);

  // Se inicializan leyendo localStorage: sobreviven a una recarga de
  // pagina. Snapshot simple (no se refresca con consultaUno() al
  // arrancar): ver el TODO en usuario-sesion.model.ts sobre su staleness.
  token = signal<string | null>(localStorage.getItem(CLAVE_TOKEN));
  usuario = signal<UsuarioSesion | null>(this.leerUsuarioGuardado());

  // Ya NO es un computed: Date.now() no es una señal, así que un computed
  // memoizaría el resultado la primera vez y nunca se reevaluaría solo
  // porque pasó el tiempo (mientras el signal token() no cambie). Como
  // función normal, cada llamada revisa el exp del token en ese instante.
  estaAutenticado(): boolean {
    const token = this.token();
    return token !== null && !this.tokenExpirado(token);
  }

  private leerUsuarioGuardado(): UsuarioSesion | null {
    const guardado = localStorage.getItem(CLAVE_USUARIO);
    if (!guardado) return null;
    try {
      return JSON.parse(guardado) as UsuarioSesion;
    } catch {
      // localStorage corrupto/editado a mano: se trata como si no hubiera nada.
      return null;
    }
  }

  // Decodifica el payload del JWT solo para leer "exp" (no verifica firma:
  // eso ya lo hace el backend en cada peticion protegida -- esto es nomás
  // para que la UI sepa si vale la pena mostrar la sesion como activa).
  private leerPayloadToken(token: string): { exp?: number } | null {
    const partes = token.split('.');
    if (partes.length !== 3) return null;
    try {
      // JWT usa base64url (RFC 4648 §5): '-' y '_' en vez de '+' y '/', y
      // sin relleno '='. atob() solo entiende base64 estandar, hay que
      // traducir antes de decodificar.
      let base64 = partes[1].replace(/-/g, '+').replace(/_/g, '/');
      const resto = base64.length % 4;
      if (resto === 2) base64 += '==';
      else if (resto === 3) base64 += '=';
      else if (resto !== 0) return null; // longitud invalida para base64url
      return JSON.parse(atob(base64));
    } catch {
      return null;
    }
  }

  // Si el payload no se puede leer (token corrupto, editado a mano, formato
  // inesperado) o no trae "exp", se trata como vencido/no autenticado en
  // vez de asumir que sigue siendo valido: un error de lectura nunca debe
  // dejar pasar una sesion que no se puede verificar.
  private tokenExpirado(token: string): boolean {
    const payload = this.leerPayloadToken(token);
    if (payload === null || typeof payload.exp !== 'number') return true;
    return Date.now() >= payload.exp * 1000;
  }

  login(email: string, clave: string) {
    return this.usuarioService.login(email, clave).pipe(
      tap((respuesta) => this.guardarSesionSiOk(respuesta))
    );
  }

  registro(datos: DatosRegistro) {
    return this.usuarioService.registro(datos).pipe(
      tap((respuesta) => this.guardarSesionSiOk(respuesta))
    );
  }

  // Mismo guardado de sesion que usaba login() antes de este cambio, ahora
  // compartido con registro() (el backend devuelve el mismo formato en los
  // dos casos: token + usuario minimo).
  private guardarSesionSiOk(respuesta: RespuestaLogin) {
    if (respuesta.Resultado === 'OK' && respuesta.token && respuesta.usuario) {
      this.token.set(respuesta.token);
      this.usuario.set(respuesta.usuario);
      localStorage.setItem(CLAVE_TOKEN, respuesta.token);
      localStorage.setItem(CLAVE_USUARIO, JSON.stringify(respuesta.usuario));
    }
  }

  // Cierre manual (botón "Cerrar sesión"): a Home.
  logout() {
    this.limpiarSesion();
    this.router.navigateByUrl('/');
  }

  // Sesión inválida detectada por el guard o por un 401 del interceptor:
  // a login, con aviso y (si la ruta es segura) la página a la que volver.
  cerrarPorSesionInvalida(returnUrl?: string) {
    this.limpiarSesion();
    const queryParams = esRutaAppValida(returnUrl)
      ? { expirada: 1, returnUrl }
      : { expirada: 1 };
    this.router.navigate(['/login'], { queryParams });
  }

  private limpiarSesion() {
    this.token.set(null);
    this.usuario.set(null);
    localStorage.removeItem(CLAVE_TOKEN);
    localStorage.removeItem(CLAVE_USUARIO);
  }
}
