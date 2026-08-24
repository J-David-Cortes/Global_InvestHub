import { Component } from '@angular/core';
import { MarketplaceCard } from './marketplace-card/marketplace-card';
import { ALGORITMOS_MOCK } from './marketplace-mock';
import { Algoritmo } from '../../modelos/algoritmo.model';

@Component({
  selector: 'app-marketplace',
  imports: [MarketplaceCard],
  templateUrl: './marketplace.html',
  styleUrl: './marketplace.css',
})
export class Marketplace {
  // Por ahora usamos los datos de prueba (mock).
  // Cuando conectemos el backend, esto se reemplazará por una llamada a un servicio.
  algoritmos: Algoritmo[] = ALGORITMOS_MOCK;
}