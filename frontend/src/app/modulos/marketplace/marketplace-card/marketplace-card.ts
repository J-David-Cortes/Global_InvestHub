import { Component, Input } from '@angular/core';
import { UpperCasePipe } from '@angular/common';
import { Algoritmo } from '../../../modelos/algoritmo.model';

@Component({
  selector: 'app-marketplace-card',
  imports: [UpperCasePipe],
  templateUrl: './marketplace-card.html',
  styleUrl: './marketplace-card.css',
})
export class MarketplaceCard {
  // @Input() significa: "este dato me lo va a entregar el componente padre"
  // El signo "!" le dice a TypeScript "confía en mí, este dato SIEMPRE llegará lleno"
  @Input() bot!: Algoritmo;

  // Este getter convierte el arreglo de números del historial
  // en el formato de "puntos" que necesita un SVG <polyline> para dibujar el gráfico.
  // Un "getter" es como una propiedad calculada: se recalcula sola cada vez que se usa.
  get puntosSparkline(): string {
    const datos = this.bot.historialRendimiento;
    const ancho = 260; // ancho del área del gráfico en píxeles
    const alto = 60;   // alto del área del gráfico en píxeles

    const minimo = Math.min(...datos);
    const maximo = Math.max(...datos);
    const rango = maximo - minimo || 1; // evita dividir entre 0

    return datos
      .map((valor, indice) => {
        // Distribuimos cada punto horizontalmente de forma pareja
        const x = (indice / (datos.length - 1)) * ancho;
        // Invertimos el eje Y porque en SVG "0" es arriba, no abajo
        const y = alto - ((valor - minimo) / rango) * alto;
        return `${x.toFixed(1)},${y.toFixed(1)}`;
      })
      .join(' ');
  }
}