import { Component, OnInit, inject, signal, computed } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { RouterLink } from '@angular/router';
import { OperacionTrading } from '../../servicios/operacion-trading';
import { UsuarioBot } from '../../servicios/usuario-bot';
import {
  ResumenDashboard,
  SharpeDashboard,
  PosicionActivaDashboard,
} from '../../modelos/dashboard.model';

@Component({
  selector: 'app-dashboard',
  imports: [RouterLink, DecimalPipe],
  templateUrl: './dashboard.html',
  styleUrl: './dashboard.css',
})
export class Dashboard implements OnInit {
  private operacionTradingService = inject(OperacionTrading);
  private usuarioBotService = inject(UsuarioBot);

  cargando = signal(true);

  resumen = signal<ResumenDashboard | null>(null);
  sharpe = signal<SharpeDashboard | null>(null);
  posicionesActivas = signal<PosicionActivaDashboard[]>([]);

  // Contamos cuántas conexiones activas tiene el usuario, a partir de
  // la misma lista que ya usa Engines.
  enginesActivos = computed(() => {
    return this.conexiones().filter((c: any) => c.activo).length;
  });

  private conexiones = signal<any[]>([]);

  ngOnInit() {
    this.cargarResumen();
    this.cargarSharpe();
    this.cargarPosicionesActivas();
    this.cargarConexiones();
  }

  cargarResumen() {
    this.operacionTradingService.resumen().subscribe({
      next: (respuesta) => {
        this.resumen.set(respuesta as ResumenDashboard);
        this.cargando.set(false);
      },
      error: (err) => {
        console.error('Error al cargar el resumen del dashboard:', err);
        this.cargando.set(false);
      },
    });
  }

  cargarSharpe() {
    this.operacionTradingService.sharpe().subscribe({
      next: (respuesta) => this.sharpe.set(respuesta as SharpeDashboard),
      error: (err) => console.error('Error al cargar el sharpe ratio:', err),
    });
  }

  cargarPosicionesActivas() {
    this.operacionTradingService.abiertas().subscribe({
      next: (respuesta) => this.posicionesActivas.set(respuesta as PosicionActivaDashboard[]),
      error: (err) => console.error('Error al cargar posiciones activas:', err),
    });
  }

  cargarConexiones() {
    this.usuarioBotService.consulta().subscribe({
      next: (respuesta: any) => this.conexiones.set(respuesta),
      error: (err) => console.error('Error al cargar conexiones:', err),
    });
  }
}