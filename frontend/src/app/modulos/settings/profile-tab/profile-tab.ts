import { Component, signal } from '@angular/core';

@Component({
  selector: 'app-profile-tab',
  imports: [],
  templateUrl: './profile-tab.html',
  styleUrl: './profile-tab.css',
})
export class ProfileTab {
  // Datos del formulario. Los inicializamos con los valores actuales del usuario.
  nombre = signal('John Smith');
  email = signal('john.smith@globalinvesthub.com');
  timezone = signal('America/New_York');
  idioma = signal('English');

  // Controla el texto/color temporal del botón "Save Changes"
  guardado = signal(false);

  actualizarNombre(valor: string) {
    this.nombre.set(valor);
  }

  actualizarEmail(valor: string) {
    this.email.set(valor);
  }

  actualizarTimezone(valor: string) {
    this.timezone.set(valor);
  }

  actualizarIdioma(valor: string) {
    this.idioma.set(valor);
  }

  guardar() {
    this.guardado.set(true);
    // Después de 2 segundos, el botón vuelve a su estado normal
    setTimeout(() => {
      this.guardado.set(false);
    }, 2000);
  }
}