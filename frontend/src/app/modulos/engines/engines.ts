import { Component, OnInit, inject, signal, computed } from '@angular/core';
import { RouterLink } from '@angular/router';
import { UsuarioBot } from '../../servicios/usuario-bot';
import { Engine, EstadoEngine } from '../../modelos/engine.model';

type FiltroEstado = 'all' | EstadoEngine;

@Component({
  selector: 'app-engines',
  imports: [RouterLink],
  templateUrl: './engines.html',
  styleUrl: './engines.css',
})
export class Engines implements OnInit {
  private usuarioBotService = inject(UsuarioBot);

  // TEMPORAL: usuario fijo hasta que exista login real (mismo criterio
  // que en el backend). Reemplazar cuando exista sesión de usuario.
  private readonly fo_usuario_temporal = 2;

  engines = signal<Engine[]>([]);
  cargando = signal(true);

  filtroActual = signal<FiltroEstado>('all');
  logAbiertoId = signal<number | null>(null);

  // TODO: no existe un motor de trading real que genere logs todavía.
  // Este mensaje reemplaza temporalmente al log real hasta que exista
  // esa conexión.
  logLines: string[] = [
    'Los logs en vivo estarán disponibles cuando conectes un motor de trading real a este engine.',
  ];

  ngOnInit() {
    this.cargarEngines();
  }

  cargarEngines() {
    this.cargando.set(true);
    this.usuarioBotService.consulta().subscribe({
      next: (respuesta: any) => {
        // El backend devuelve los nombres reales (botNombre, botPlataforma, etc.)
        // -- los traducimos aquí a la forma que espera nuestra interfaz Engine.
        const enginesTraducidos: Engine[] = respuesta.map((fila: any) => ({
          id: fila.id,
          botId: fila.botId,
          nombre: fila.botNombre,
          plataforma: fila.botPlataforma,
          categoria: fila.botCategoria,
          brokerNombre: fila.brokerNombre,
          activo: fila.activo,
          fechaActivacion: fila.fechaActivacion,
          fechaDesactivacion: fila.fechaDesactivacion,
        }));
        this.engines.set(enginesTraducidos);
        this.cargando.set(false);
      },
      error: (err) => {
        console.error('Error al cargar los engines:', err);
        this.cargando.set(false);
      },
    });
  }

  // Traduce el booleano real 'activo' al estado visual que ya diseñamos.
  // TODO: cuando exista una columna/estado real de "paused" en la BD,
  // reemplazar esta traducción simplificada por el dato real.
  estadoDe(engine: Engine): EstadoEngine {
    return engine.activo ? 'running' : 'stopped';
  }

  enginesFiltrados = computed(() => {
    const filtro = this.filtroActual();
    if (filtro === 'all') return this.engines();
    return this.engines().filter((e) => this.estadoDe(e) === filtro);
  });

  contarPorEstado(estado: EstadoEngine): number {
    return this.engines().filter((e) => this.estadoDe(e) === estado).length;
  }

  seleccionarFiltro(filtro: FiltroEstado) {
    this.filtroActual.set(filtro);
  }

  // Calcula "cuánto tiempo lleva activo" a partir de fechaActivacion,
  // en vez de guardar ese dato -- siempre está actualizado al momento
  // de mostrarlo.
  tiempoActivoDe(engine: Engine): string {
    if (!engine.activo) return '—';
    const inicio = new Date(engine.fechaActivacion).getTime();
    const ahora = Date.now();
    const diffMs = ahora - inicio;
    const dias = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    const horas = Math.floor((diffMs / (1000 * 60 * 60)) % 24);
    return `${dias}d ${horas}h`;
  }

  cambiarEstado(id: number, nuevoActivo: boolean) {
    this.usuarioBotService.editar(id, { activo: nuevoActivo ? 1 : 0 }).subscribe({
      next: () => {
        // Actualizamos el signal local sin tener que recargar todo desde el backend
        this.engines.update((lista) =>
          lista.map((e) => (e.id === id ? { ...e, activo: nuevoActivo } : e))
        );
      },
      error: (err) => console.error('Error al cambiar estado del engine:', err),
    });
  }

  alternarLog(id: number) {
    this.logAbiertoId.update((actual) => (actual === id ? null : id));
  }
}