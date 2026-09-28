import { Component, inject, signal } from '@angular/core';
import { Router } from '@angular/router';
import { Auth } from '../../servicios/auth';

@Component({
  selector: 'app-login',
  imports: [],
  templateUrl: './login.html',
  styleUrl: './login.css',
})
export class Login {
  private auth = inject(Auth);
  private router = inject(Router);

  email = signal('');
  clave = signal('');
  enviando = signal(false);
  errorMensaje = signal<string | null>(null);

  puedeEnviar(): boolean {
    return !this.enviando() && this.email().trim() !== '' && this.clave() !== '';
  }

  actualizarEmail(valor: string) {
    this.email.set(valor);
  }

  actualizarClave(valor: string) {
    this.clave.set(valor);
  }

  alEnviar(evento: Event) {
    evento.preventDefault();
    if (!this.puedeEnviar()) return;

    this.enviando.set(true);
    this.errorMensaje.set(null);

    this.auth.login(this.email().trim(), this.clave()).subscribe({
      next: (respuesta) => {
        this.enviando.set(false);
        if (respuesta.Resultado === 'Error') {
          this.errorMensaje.set(respuesta.mensaje ?? 'No se pudo iniciar sesión');
          return;
        }
        this.router.navigateByUrl('/app');
      },
      error: (err) => {
        console.error('Error al iniciar sesión:', err);
        this.enviando.set(false);
        this.errorMensaje.set('No se pudo conectar con el servidor');
      },
    });
  }
}
