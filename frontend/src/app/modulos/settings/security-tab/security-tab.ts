import { Component, signal } from '@angular/core';
import { Sesion } from '../../../modelos/sesion.model';

@Component({
  selector: 'app-security-tab',
  imports: [],
  templateUrl: './security-tab.html',
  styleUrl: './security-tab.css',
})
export class SecurityTab {
  mostrarPassword = signal(false);
  dosFA = signal(true);

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
    // Por ahora solo la quitamos del arreglo local (mock).
    // Cuando conectemos el backend, aquí iría una llamada real a la API.
    this.sesiones = this.sesiones.filter((s) => s.dispositivo !== dispositivo);
  }
}