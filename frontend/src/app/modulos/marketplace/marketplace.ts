import { Component, OnInit, inject, signal, computed } from '@angular/core';
import { MarketplaceCard } from './marketplace-card/marketplace-card';
import { BotInversion } from '../../servicios/bot-inversion';
import { UsuarioBot } from '../../servicios/usuario-bot';
import { Algoritmo, CategoriaAlgoritmo } from '../../modelos/algoritmo.model';
import { SubscribeModal } from './subscribe-modal/subscribe-modal';

// "All" no es una categoría real del modelo, así que creamos un tipo aparte
// que combina las categorías reales + la opción especial "All"
type CategoriaFiltro = CategoriaAlgoritmo | 'All';
type OpcionOrden = 'return' | 'sharpe' | 'price' | 'popularity';

@Component({
  selector: 'app-marketplace',
  imports: [MarketplaceCard, SubscribeModal],
  templateUrl: './marketplace.html',
  styleUrl: './marketplace.css',
})
export class Marketplace implements OnInit {
  private botInversionService = inject(BotInversion);
  private usuarioBotService = inject(UsuarioBot);

  // Lista fija de categorías para dibujar las pills (con "All" primero)
  categorias: CategoriaFiltro[] = ['All', 'Forex', 'Crypto', 'Equities', 'Commodities', 'Options'];

  // ----- Datos que vienen del backend -----
  // Empieza vacío: todavía no hemos recibido respuesta del servidor
  algoritmos = signal<Algoritmo[]>([]);
  cargando = signal(true);

  // ids de los bots que el usuario ya tiene activos (conexiones con activo=1).
  // Se reconstruye siempre desde el backend, nunca se edita a mano.
  botsActivosIds = signal<Set<number>>(new Set());

  // Bot cuyo modal de suscripcion esta abierto; null = modal cerrado.
  botModal = signal<Algoritmo | null>(null);

  // ----- SIGNALS: lo que el usuario puede cambiar interactuando con la página -----
  terminoBusqueda = signal('');
  categoriaSeleccionada = signal<CategoriaFiltro>('All');
  ordenSeleccionado = signal<OpcionOrden>('return');

  // ngOnInit se ejecuta UNA VEZ, apenas Angular termina de crear el componente
  ngOnInit() {
    this.cargarAlgoritmos();
    this.cargarBotsActivos();
  }

  cargarBotsActivos() {
    this.usuarioBotService.consulta().subscribe({
      next: (respuesta) => {
        const conexiones = respuesta as { botId: number }[];
        this.botsActivosIds.set(new Set(conexiones.map((c) => c.botId)));
      },
      error: (err) => console.error('Error al cargar las conexiones del usuario:', err),
    });
  }

  cargarAlgoritmos() {
    this.cargando.set(true);
    this.botInversionService.consulta().subscribe({
      next: (respuesta) => {
        this.algoritmos.set(respuesta as Algoritmo[]);
        this.cargando.set(false);
      },
      error: (err) => {
        console.error('Error al cargar los algoritmos del Marketplace:', err);
        this.cargando.set(false);
      },
    });
  }

  // ----- COMPUTED: se recalcula solo cada vez que cambia alguno de los signals de arriba -----
  algoritmosFiltrados = computed(() => {
    const termino = this.terminoBusqueda().toLowerCase().trim();
    const categoria = this.categoriaSeleccionada();
    const orden = this.ordenSeleccionado();

    return this.algoritmos()
      // 1. Filtrar por categoría (si es "All", no descarta nada)
      .filter((bot) => categoria === 'All' || bot.categoria === categoria)
      // 2. Filtrar por texto de búsqueda (nombre, desarrollador o tags)
      .filter((bot) => {
        if (!termino) return true;
        return (
          bot.nombre.toLowerCase().includes(termino) ||
          bot.desarrollador?.toLowerCase().includes(termino) ||
          bot.tags?.some((tag) => tag.toLowerCase().includes(termino))
        );
      })
      // 3. Ordenar según la opción elegida
      .sort((a, b) => {
        switch (orden) {
          case 'return':
            return (b.rendimientoAnual ?? 0) - (a.rendimientoAnual ?? 0);
          case 'sharpe':
            return (b.sharpeRatio ?? 0) - (a.sharpeRatio ?? 0);
          case 'price':
          // Los bots no-VIP no tienen precio propio (van incluidos en el plan),
          // así que los tratamos como "0" para que queden primero al ordenar
          // de menor a mayor precio -- tiene sentido mostrar lo gratis/incluido
          // antes que lo que cuesta aparte.
          return (a.precioIndividual ?? 0) - (b.precioIndividual ?? 0);
          case 'popularity':
            return (b.numeroSuscriptores ?? 0) - (a.numeroSuscriptores ?? 0);
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

  abrirModal(bot: Algoritmo) {
    this.botModal.set(bot);
  }

  cerrarModal() {
    this.botModal.set(null);
  }

  // Suscripcion confirmada por el backend: se cierra el modal y se vuelve a
  // pedir el estado real (mismo criterio que Engines) en vez de tocar el Set.
  // Entre el cierre y la respuesta la tarjeta puede mostrar "Subscribe" unos
  // milisegundos: se acepta a cambio de no depender de la recarga para cerrar.
  // TODO: numeroSuscriptores (contador de la tarjeta) viene de
  // bot_inversion.consulta() y no se refresca hasta recargar la pagina.
  alSuscribir() {
    this.cerrarModal();
    this.cargarBotsActivos();
  }
}