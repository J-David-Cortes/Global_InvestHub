import { Component, Input, signal } from '@angular/core';
import { UpperCasePipe } from '@angular/common';
import { Algoritmo } from '../../../modelos/algoritmo.model';

@Component({
  selector: 'app-marketplace-card',
  imports: [UpperCasePipe],
  templateUrl: './marketplace-card.html',
  styleUrl: './marketplace-card.css',
})
export class MarketplaceCard {
  @Input() bot!: Algoritmo;

  estaSuscrito = signal(false);

  // Ancho y alto del gráfico, los definimos una sola vez para reutilizarlos
  private readonly anchoSparkline = 260;
  private readonly altoSparkline = 60;

  get puntosSparkline(): string {
    const datos = this.bot.historialRendimiento;
    const minimo = Math.min(...datos);
    const maximo = Math.max(...datos);
    const rango = maximo - minimo || 1;

    return datos
      .map((valor, indice) => {
        const x = (indice / (datos.length - 1)) * this.anchoSparkline;
        const y = this.altoSparkline - ((valor - minimo) / rango) * this.altoSparkline;
        return `${x.toFixed(1)},${y.toFixed(1)}`;
      })
      .join(' ');
  }

  // Nuevo: toma los mismos puntos de la línea, y les agrega 2 esquinas
  // abajo para "cerrar" la forma y poder rellenarla con degradado.
  get puntosRelleno(): string {
    const inicioAbajo = `0,${this.altoSparkline}`;
    const finAbajo = `${this.anchoSparkline},${this.altoSparkline}`;
    return `${inicioAbajo} ${this.puntosSparkline} ${finAbajo}`;
  }

  // ID único para el degradado, evita que todas las tarjetas
  // compartan por accidente la misma definición de degradado SVG
  get idDegradado(): string {
    return `degradado-sparkline-${this.bot.id}`;
  }

  alternarSuscripcion() {
    this.estaSuscrito.update((valorActual) => !valorActual);
  }
}