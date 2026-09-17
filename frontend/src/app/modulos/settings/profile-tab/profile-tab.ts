import { Component, OnInit, inject, signal } from '@angular/core';
import { Usuario } from '../../../servicios/usuario';
import { UsuarioPerfil } from '../../../modelos/usuario.model';

@Component({
  selector: 'app-profile-tab',
  imports: [],
  templateUrl: './profile-tab.html',
  styleUrl: './profile-tab.css',
})
export class ProfileTab implements OnInit {
  private usuarioService = inject(Usuario);

  cargando = signal(true);

  // Datos reales del backend
  nombre = signal('');
  email = signal('');
  nombreNivel = signal('');

  // TODO: sin respaldo real en la BD todavía -- placeholder hasta que
  // se agreguen columnas de timezone/idioma/avatar a la tabla usuario.
  timezone = signal('America/New_York');
  idioma = signal('English');

  guardado = signal(false);

  ngOnInit() {
    this.cargarPerfil();
  }

  cargarPerfil() {
    this.cargando.set(true);
    this.usuarioService.consultaUno().subscribe({
      next: (respuesta: any) => {
        this.nombre.set(respuesta.nombre);
        this.email.set(respuesta.email);
        this.nombreNivel.set(respuesta.nombre_nivel);
        this.cargando.set(false);
      },
      error: (err) => {
        console.error('Error al cargar el perfil:', err);
        this.cargando.set(false);
      },
    });
  }

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
    // Ahora sí hace una petición real de guardado, no solo un signal local
    this.usuarioService.editar(2, {
      nombre: this.nombre(),
      email: this.email(),
      fo_permiso: 4, // TODO: usar el valor real del usuario, no fijo
    }).subscribe({
      next: () => {
        this.guardado.set(true);
        setTimeout(() => this.guardado.set(false), 2000);
      },
      error: (err) => console.error('Error al guardar el perfil:', err),
    });
  }
}