import { Component, OnInit, inject, signal } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { OperacionTrading } from '../../servicios/operacion-trading';
import { ResumenPortfolio, PosicionAbierta, PosicionCerrada } from '../../modelos/portfolio.model';

@Component({
  selector: 'app-portfolio',
  imports: [DecimalPipe],
  templateUrl: './portfolio.html',
  styleUrl: './portfolio.css',
})
export class Portfolio implements OnInit {
  private operacionTradingService = inject(OperacionTrading);

  cargando = signal(true);

  resumen = signal<ResumenPortfolio | null>(null);
  posicionesAbiertas = signal<PosicionAbierta[]>([]);
  posicionesCerradas = signal<PosicionCerrada[]>([]);

  ngOnInit() {
    this.cargarResumen();
    this.cargarAbiertas();
    this.cargarCerradas();
  }

  cargarResumen() {
    this.operacionTradingService.resumen().subscribe({
      next: (respuesta) => {
        this.resumen.set(respuesta as ResumenPortfolio);
        this.cargando.set(false);
      },
      error: (err) => {
        console.error('Error al cargar el resumen de Portfolio:', err);
        this.cargando.set(false);
      },
    });
  }

  cargarAbiertas() {
    this.operacionTradingService.abiertas().subscribe({
      next: (respuesta) => {
        this.posicionesAbiertas.set(respuesta as PosicionAbierta[]);
      },
      error: (err) => console.error('Error al cargar posiciones abiertas:', err),
    });
  }

  cargarCerradas() {
    this.operacionTradingService.cerradas().subscribe({
      next: (respuesta) => {
        this.posicionesCerradas.set(respuesta as PosicionCerrada[]);
      },
      error: (err) => console.error('Error al cargar posiciones cerradas:', err),
    });
  }
}