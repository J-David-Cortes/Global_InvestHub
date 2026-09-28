import { HttpClient } from '@angular/common/http';
import { Injectable, computed, inject, signal } from '@angular/core';
import { Router } from '@angular/router';
import { tap } from 'rxjs';
import { Usuario } from './usuario';
import { RespuestaLogin, UsuarioSesion } from '../modelos/usuario-sesion.model';

const CLAVE_TOKEN = 'auth_token';
const CLAVE_USUARIO = 'auth_usuario';

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

  estaAutenticado = computed(() => this.token() !== null);

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

  login(email: string, clave: string) {
    return this.usuarioService.login(email, clave).pipe(
      tap((respuesta: RespuestaLogin) => {
        if (respuesta.Resultado === 'OK' && respuesta.token && respuesta.usuario) {
          this.token.set(respuesta.token);
          this.usuario.set(respuesta.usuario);
          localStorage.setItem(CLAVE_TOKEN, respuesta.token);
          localStorage.setItem(CLAVE_USUARIO, JSON.stringify(respuesta.usuario));
        }
      })
    );
  }

  logout() {
    this.token.set(null);
    this.usuario.set(null);
    localStorage.removeItem(CLAVE_TOKEN);
    localStorage.removeItem(CLAVE_USUARIO);
    this.router.navigateByUrl('/login');
  }
}
