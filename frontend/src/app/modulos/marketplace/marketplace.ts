import { Component, signal, computed } from '@angular/core';
import { MarketplaceCard } from './marketplace-card/marketplace-card';
import { ALGORITMOS_MOCK } from './marketplace-mock';
import { CategoriaAlgoritmo } from '../../modelos/algoritmo.model';

// "All" no es una categoría real del modelo, así que creamos un tipo aparte
// que combina las categorías reales + la opción especial "All"
type CategoriaFiltro = CategoriaAlgoritmo | 'All';
type OpcionOrden = 'return' | 'sharpe' | 'price' | 'popularity';

@Component({
  selector: 'app-marketplace',
  imports: [MarketplaceCard],
  templateUrl: './marketplace.html',
  styleUrl: './marketplace.css',
})
export class Marketplace {
  // Lista fija de categorías para dibujar las pills (con "All" primero)
  categorias: CategoriaFiltro[] = ['All', 'Forex', 'Crypto', 'Equities', 'Commodities', 'Options'];

  // ----- SIGNALS: lo que el usuario puede cambiar interactuando con la página -----
  terminoBusqueda = signal('');
  categoriaSeleccionada = signal<CategoriaFiltro>('All');
  ordenSeleccionado = signal<OpcionOrden>('return');

  // ----- COMPUTED: se recalcula solo cada vez que cambia alguno de los signals de arriba -----
  algoritmosFiltrados = computed(() => {
    const termino = this.terminoBusqueda().toLowerCase().trim();
    const categoria = this.categoriaSeleccionada();
    const orden = this.ordenSeleccionado();

    return ALGORITMOS_MOCK
      // 1. Filtrar por categoría (si es "All", no descarta nada)
      .filter((bot) => categoria === 'All' || bot.categoria === categoria)
      // 2. Filtrar por texto de búsqueda (nombre, desarrollador o tags)
      .filter((bot) => {
        if (!termino) return true;
        return (
          bot.nombre.toLowerCase().includes(termino) ||
          bot.desarrollador.toLowerCase().includes(termino) ||
          bot.tags.some((tag) => tag.toLowerCase().includes(termino))
        );
      })
      // 3. Ordenar según la opción elegida
      .sort((a, b) => {
        switch (orden) {
          case 'return':
            return b.rendimientoAnual - a.rendimientoAnual;
          case 'sharpe':
            return b.sharpeRatio - a.sharpeRatio;
          case 'price':
            return a.precioMensual - b.precioMensual;
          case 'popularity':
            return b.numeroSuscriptores - a.numeroSuscriptores;
        }
      });
  });

  // ----- Funciones que el HTML llama cuando el usuario interactúa -----
  actualizarBusqueda(valor: string) {
    this.terminoBusqueda.set(valor);
  }

  seleccionarCategoria(categoria: CategoriaFiltro) {
    this.categoriaSeleccionada.set(categoria);
  }

  actualizarOrden(valor: string) {
    this.ordenSeleccionado.set(valor as OpcionOrden);
  }
}