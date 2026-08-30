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

  // Estado de suscripción, propio de CADA tarjeta (empieza en "false" = no suscrito)
  estaSuscrito = signal(false);

  get puntosSparkline(): string {
    const datos = this.bot.historialRendimiento;
    const ancho = 260;
    const alto = 60;

    const minimo = Math.min(...datos);
    const maximo = Math.max(...datos);
    const rango = maximo - minimo || 1;

    return datos
      .map((valor, indice) => {
        const x = (indice / (datos.length - 1)) * ancho;
        const y = alto - ((valor - minimo) / rango) * alto;
        return `${x.toFixed(1)},${y.toFixed(1)}`;
      })
      .join(' ');
  }

  // Se ejecuta cuando el usuario hace clic en el botón
  alternarSuscripcion() {
    // .update() toma el valor ACTUAL del signal y lo transforma al nuevo valor
    this.estaSuscrito.update((valorActual) => !valorActual);
  }
}