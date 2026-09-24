import { Component, OnInit, inject, signal, computed } from '@angular/core';
import { Suscripcion } from '../../../servicios/suscripcion';
import { UsuarioSuscripcion } from '../../../servicios/usuario-suscripcion';
import {
  PlanCatalogo,
  PlanActual,
  CARACTERISTICAS_PLAN,
  viñetaLimiteBots,
} from '../../../modelos/plan.model';

@Component({
  selector: 'app-billing-tab',
  imports: [],
  templateUrl: './billing-tab.html',
  styleUrl: './billing-tab.css',
})
export class BillingTab implements OnInit {
  private suscripcionService = inject(Suscripcion);
  private usuarioSuscripcionService = inject(UsuarioSuscripcion);

  cargando = signal(true);

  planActual = signal<PlanActual | null>(null);
  todosLosPlanes = signal<PlanCatalogo[]>([]);

  ngOnInit() {
    this.cargarPlanActual();
    this.cargarListaPlanes();
  }

  cargarPlanActual() {
    this.usuarioSuscripcionService.planActual().subscribe({
      next: (respuesta: any) => {
        this.planActual.set(respuesta);
        this.cargando.set(false);
      },
      error: (err) => {
        console.error('Error al cargar el plan actual:', err);
        this.cargando.set(false);
      },
    });
  }

  cargarListaPlanes() {
    this.suscripcionService.listaPlanes().subscribe({
      next: (respuesta: any) => {
        this.todosLosPlanes.set(respuesta);
      },
      error: (err) => console.error('Error al cargar la lista de planes:', err),
    });
  }

  // Solo mostramos Trial si es el plan actual del usuario -- nadie lo
  // "elige" desde esta pantalla, se otorga aparte.
  planesVisibles = computed(() => {
    const actual = this.planActual();
    return this.todosLosPlanes().filter((plan) => {
      if (plan.nombre !== 'Trial') return true;
      return actual?.nombre === 'Trial';
    });
  });

  esPlanActual(plan: PlanCatalogo): boolean {
    return this.planActual()?.id === plan.id;
  }

  caracteristicasDe(plan: PlanCatalogo): string[] {
    const viñeta = viñetaLimiteBots(plan.limiteBots);
    const resto = CARACTERISTICAS_PLAN[plan.nombre] ?? [];
    return [viñeta, ...resto];
  }

  esUpgrade(plan: PlanCatalogo): boolean {
    const precioActual = this.planActual()?.precio ?? 0;
    return plan.precio > precioActual;
  }
}