import { Component, inject, signal } from '@angular/core';
import { Usuario } from '../../../servicios/usuario';
import { Sesion } from '../../../modelos/sesion.model';

@Component({
  selector: 'app-security-tab',
  imports: [],
  templateUrl: './security-tab.html',
  styleUrl: './security-tab.css',
})
export class SecurityTab {
  private usuarioService = inject(Usuario);

  mostrarPassword = signal(false);
  dosFA = signal(true);

  // Campos del formulario de cambio de contraseña
  claveActual = signal('');
  claveNueva = signal('');
  claveConfirmar = signal('');

  // Mensaje de resultado (éxito o error) tras intentar cambiar la clave
  mensajeClave = signal<{ tipo: 'exito' | 'error'; texto: string } | null>(null);

  sesiones: Sesion[] = [
    { dispositivo: 'Chrome — macOS', ubicacion: 'New York, US', tiempo: 'Active now', esActual: true },
    { dispositivo: 'Safari — iPhone 15', ubicacion: 'New York, US', tiempo: '2 hours ago', esActual: false },
    { dispositivo: 'Firefox — Windows', ubicacion: 'London, UK', tiempo: '3 days ago', esActual: false },
  ];

  alternarMostrarPassword() {
    this.mostrarPassword.update((valor) => !valor);
  }

  alternarDosFA() {
    this.dosFA.update((valor) => !valor);
  }

  revocarSesion(dispositivo: string) {
    this.sesiones = this.sesiones.filter((s) => s.dispositivo !== dispositivo);
  }

  actualizarClaveActual(valor: string) {
    this.claveActual.set(valor);
  }

  actualizarClaveNueva(valor: string) {
    this.claveNueva.set(valor);
  }

  actualizarClaveConfirmar(valor: string) {
    this.claveConfirmar.set(valor);
  }

  actualizarPassword() {
    this.mensajeClave.set(null);

    // Validación en el frontend: Confirm debe coincidir con New
    if (this.claveNueva() !== this.claveConfirmar()) {
      this.mensajeClave.set({ tipo: 'error', texto: 'La confirmación no coincide con la contraseña nueva.' });
      return;
    }

    this.usuarioService.cambiarClave(this.claveActual(), this.claveNueva()).subscribe({
      next: (respuesta: any) => {
        if (respuesta.Resultado === 'OK') {
          this.mensajeClave.set({ tipo: 'exito', texto: respuesta.mensaje });
          this.claveActual.set('');
          this.claveNueva.set('');
          this.claveConfirmar.set('');
        } else {
          this.mensajeClave.set({ tipo: 'error', texto: respuesta.mensaje });
        }
      },
      error: (err) => {
        console.error('Error al cambiar la contraseña:', err);
        this.mensajeClave.set({ tipo: 'error', texto: 'Ocurrió un error de conexión. Intenta de nuevo.' });
      },
    });
  }
}