import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { Auth } from '../../servicios/auth';
import { UsuarioSuscripcion } from '../../servicios/usuario-suscripcion';
import { PlanActual } from '../../modelos/plan.model';

@Component({
  selector: 'app-nav',
  imports: [RouterLink, RouterLinkActive],
  templateUrl: './nav.html',
  styleUrl: './nav.css',
})
export class Nav implements OnInit {
  auth = inject(Auth);
  private usuarioSuscripcionService = inject(UsuarioSuscripcion);

  planActual = signal<PlanActual | null>(null);

  // Iniciales del avatar: primera letra de la primera y de la ultima
  // palabra del nombre real (una sola palabra -> solo esa letra).
  iniciales = computed(() => {
    const nombre = this.auth.usuario()?.nombre?.trim();
    if (!nombre) return '';
    const palabras = nombre.split(/\s+/);
    const primera = palabras[0][0];
    const ultima = palabras[palabras.length - 1][0];
    return (palabras.length > 1 ? primera + ultima : primera).toUpperCase();
  });

  ngOnInit() {
    this.usuarioSuscripcionService.planActual().subscribe({
      next: (respuesta: any) => this.planActual.set(respuesta),
      error: (err) => console.error('Error al cargar el plan del usuario:', err),
    });
  }
}
