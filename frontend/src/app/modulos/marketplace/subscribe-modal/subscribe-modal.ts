import { Component, EventEmitter, HostListener, Input, OnInit, Output, computed, inject, signal } from '@angular/core';
import { catchError, forkJoin, of } from 'rxjs';
import { Broker } from '../../../servicios/broker';
import { UsuarioBot } from '../../../servicios/usuario-bot';
import { Algoritmo } from '../../../modelos/algoritmo.model';
import { BrokerOpcion } from '../../../modelos/broker.model';

// Valor de la opcion "Otro (especificar)" del <select>. Los ids de broker son
// numericos, asi que no puede chocar con ninguno.
const OPCION_OTRO = 'otro';

// Igual que la colacion de la BD (utf8mb4_unicode_ci): sin mayusculas, tildes
// ni espacios sobrantes.
function normalizar(texto: string): string {
  return texto.trim().toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
}

@Component({
  selector: 'app-subscribe-modal',
  templateUrl: './subscribe-modal.html',
  styleUrl: './subscribe-modal.css',
})
export class SubscribeModal implements OnInit {
  private brokerService = inject(Broker);
  private usuarioBotService = inject(UsuarioBot);

  @Input() bot!: Pick<Algoritmo, 'id' | 'nombre' | 'plataforma'>;
  // Suscripcion creada: el padre decide cerrar y refrescar.
  @Output() suscrito = new EventEmitter<void>();
  // El usuario cerro sin confirmar (cancelar, X, fondo o Escape).
  @Output() cerrado = new EventEmitter<void>();

  brokers = signal<BrokerOpcion[]>([]);
  brokerSeleccionadoId = signal<number | null>(null);
  readonly opcionOtro = OPCION_OTRO;
  // Modo "Otro": el broker no esta en la lista y el usuario escribe su nombre.
  modoOtro = signal(false);
  brokerNuevo = signal('');
  apiKey = signal('');
  cargando = signal(true);
  enviando = signal(false);
  errorMensaje = signal<string | null>(null);

  // ¿Lo escrito en "Otro" ya existe en la lista? Solo una ayuda de UX: la
  // unicidad real la garantiza el backend (obtenerOCrear + indice UNIQUE).
  brokerCoincidente = computed(() => {
    if (!this.modoOtro()) return null;
    const escrito = normalizar(this.brokerNuevo());
    if (escrito === '') return null;
    return this.brokers().find((b) => normalizar(b.nombre) === escrito) ?? null;
  });

  puedeConfirmar = computed(() => {
    if (this.cargando() || this.enviando() || this.apiKey().trim() === '') return false;
    return this.modoOtro()
      ? this.brokerNuevo().trim() !== ''
      : this.brokerSeleccionadoId() !== null;
  });

  ngOnInit() {
    forkJoin({
      brokers: this.brokerService.consulta(),
      // Si estadoBroker falla no es critico: solo se pierde la sugerencia.
      estado: this.usuarioBotService.estadoBroker().pipe(catchError(() => of(null))),
    }).subscribe({
      next: ({ brokers, estado }) => {
        const opciones: BrokerOpcion[] = (brokers as any[]).map((fila) => ({
          id: Number(fila.id_broker),
          nombre: fila.nombre,
        }));
        this.brokers.set(opciones);

        const sugerido = estado?.ultimoBrokerId ?? null;
        if (sugerido !== null && opciones.some((b) => b.id === sugerido)) {
          this.brokerSeleccionadoId.set(sugerido);
        }
        this.cargando.set(false);
      },
      error: (err) => {
        console.error('Error al cargar los brokers:', err);
        this.errorMensaje.set('No se pudieron cargar los brokers');
        this.cargando.set(false);
      },
    });
  }

  seleccionarBroker(valor: string) {
    if (valor === OPCION_OTRO) {
      this.modoOtro.set(true);
      this.brokerSeleccionadoId.set(null);
      return;
    }
    this.modoOtro.set(false);
    this.brokerSeleccionadoId.set(valor === '' ? null : Number(valor));
  }

  actualizarBrokerNuevo(valor: string) {
    this.brokerNuevo.set(valor);
  }

  actualizarApiKey(valor: string) {
    this.apiKey.set(valor);
  }

  alEnviar(evento: Event) {
    evento.preventDefault();
    this.confirmar();
  }

  confirmar() {
    if (!this.puedeConfirmar()) return;

    const apiKey = this.apiKey().trim();
    // En "Otro": si el nombre ya existe en la lista se usa ese id (sin crear
    // nada); si no, el backend lo crea junto con la suscripcion.
    const brokerId = this.modoOtro()
      ? (this.brokerCoincidente()?.id ?? null)
      : this.brokerSeleccionadoId();
    const peticion =
      brokerId !== null
        ? this.usuarioBotService.suscribir(this.bot.id, brokerId, apiKey)
        : this.usuarioBotService.suscribirConBrokerNuevo(this.bot.id, this.brokerNuevo().trim(), apiKey);

    this.enviando.set(true);
    this.errorMensaje.set(null);

    peticion.subscribe({
      next: (respuesta) => {
        this.enviando.set(false);
        // El backend responde 200 aun cuando rechaza (duplicado, limite del
        // plan): se detecta por Resultado, y el modal se queda abierto.
        if (respuesta.Resultado === 'Error') {
          this.errorMensaje.set(respuesta.mensaje);
          return;
        }
        this.suscrito.emit();
      },
      error: (err) => {
        console.error('Error al suscribir:', err);
        this.enviando.set(false);
        this.errorMensaje.set('No se pudo conectar con el servidor');
      },
    });
  }

  // No se puede cerrar con una peticion en vuelo: el componente se
  // destruiria y el padre nunca recibiria el "suscrito".
  cerrar() {
    if (this.enviando()) return;
    this.cerrado.emit();
  }

  // Solo cierra si el clic cae en el fondo mismo, no dentro del panel.
  alClickFondo(evento: MouseEvent) {
    if (evento.target === evento.currentTarget) this.cerrar();
  }

  @HostListener('document:keydown.escape')
  alEscape() {
    this.cerrar();
  }
}
