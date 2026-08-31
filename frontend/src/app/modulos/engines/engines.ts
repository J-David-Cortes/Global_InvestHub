import { Component, signal, computed } from '@angular/core';
import { RouterLink } from '@angular/router';
import { ENGINES_MOCK, LOG_LINES_MOCK } from './engines-mock';
import { Engine, EstadoEngine } from '../../modelos/engine.model';

type FiltroEstado = 'all' | EstadoEngine;

@Component({
  selector: 'app-engines',
  imports: [RouterLink],
  templateUrl: './engines.html',
  styleUrl: './engines.css',
})
export class Engines {
  // La lista completa vive en un signal porque SÍ va a mutar
  // (cuando el usuario le da Play/Pause/Stop a un engine)
  engines = signal<Engine[]>(ENGINES_MOCK);

  filtroActual = signal<FiltroEstado>('all');
  logAbiertoId = signal<number | null>(null);

  logLines = LOG_LINES_MOCK;

  // Lista ya filtrada según la pill seleccionada
  enginesFiltrados = computed(() => {
    const filtro = this.filtroActual();
    if (filtro === 'all') return this.engines();
    return this.engines().filter((e) => e.estado === filtro);
  });

  // Cuenta cuántos engines hay en cada estado (para mostrar en las pills y tarjetas resumen)
  contarPorEstado(estado: EstadoEngine): number {
    return this.engines().filter((e) => e.estado === estado).length;
  }

  seleccionarFiltro(filtro: FiltroEstado) {
    this.filtroActual.set(filtro);
  }

  // Cambia el estado de UN engine específico, sin afectar a los demás
  cambiarEstado(id: number, nuevoEstado: EstadoEngine) {
    this.engines.update((listaActual) =>
      listaActual.map((engine) =>
        engine.id === id ? { ...engine, estado: nuevoEstado } : engine
      )
    );
  }

  // Abre o cierra el panel de logs de un engine (solo uno a la vez)
  alternarLog(id: number) {
    this.logAbiertoId.update((actual) => (actual === id ? null : id));
  }
}