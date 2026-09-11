import { Component } from '@angular/core';
import { Plan, Pago } from '../../../modelos/plan.model';

@Component({
  selector: 'app-billing-tab',
  imports: [],
  templateUrl: './billing-tab.html',
  styleUrl: './billing-tab.css',
})
export class BillingTab {
  planes: Plan[] = [
    {
      nombre: 'Starter',
      precio: 0,
      descripcion: 'Para comenzar',
      caracteristicas: ['3 engines activos', 'Marketplace básico', 'Soporte por email'],
      esActual: false,
    },
    {
      nombre: 'Pro',
      precio: 79,
      descripcion: 'Más popular',
      caracteristicas: ['Engines ilimitados', 'Todo el Marketplace', 'Analytics avanzado', 'Soporte prioritario'],
      esActual: true,
    },
    {
      nombre: 'Enterprise',
      precio: 299,
      descripcion: 'Para instituciones',
      caracteristicas: ['API dedicada', 'SLA garantizado', 'Gestor de cuenta', 'Infraestructura dedicada'],
      esActual: false,
    },
  ];

  pagos: Pago[] = [
    { fecha: 'Aug 1, 2026', descripcion: 'Pro Plan — Agosto 2026', monto: '$79.00', estado: 'Paid' },
    { fecha: 'Jul 1, 2026', descripcion: 'Pro Plan — Julio 2026', monto: '$79.00', estado: 'Paid' },
    { fecha: 'Jun 1, 2026', descripcion: 'Pro Plan — Junio 2026', monto: '$79.00', estado: 'Paid' },
  ];

  // El precio del plan Pro actual, para comparar y decidir "Upgrade" vs "Downgrade"
  private precioPlanActual = this.planes.find((p) => p.esActual)?.precio ?? 0;

  esUpgrade(plan: Plan): boolean {
    return plan.precio > this.precioPlanActual;
  }
}