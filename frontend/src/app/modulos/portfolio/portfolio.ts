import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import {
  METRICAS_MOCK,
  COLORES_ASIGNACION,
  HOLDINGS_MOCK,
  POSICIONES_CERRADAS_MOCK,
} from './portfolio-mock';

@Component({
  selector: 'app-portfolio',
  imports: [RouterLink],
  templateUrl: './portfolio.html',
  styleUrl: './portfolio.css',
})
export class Portfolio {
  metricas = METRICAS_MOCK;
  colores = COLORES_ASIGNACION;
  holdings = HOLDINGS_MOCK;
  posicionesCerradas = POSICIONES_CERRADAS_MOCK;

  // Excluimos "Cash Reserve" de la tabla de posiciones abiertas
  // (Figma lo hace filtrando por algoritmo !== "USD")
  get holdingsConOperacion() {
    return this.holdings.filter((h) => h.algoritmo !== 'USD');
  }

  // Le asigna a cada holding el color que le corresponde según su posición en el arreglo
  colorDe(indice: number): string {
    return this.colores[indice % this.colores.length];
  }
}