import { Component, computed, inject, signal } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { finalize } from 'rxjs';
import { Auth } from '../../servicios/auth';
import { DatosRegistro } from '../../modelos/usuario-sesion.model';
import { VERSION_TEXTOS_LEGALES } from '../../config/legal';

@Component({
  selector: 'app-registro',
  imports: [RouterLink],
  templateUrl: './registro.html',
  styleUrl: './registro.css',
})
export class Registro {
  private auth = inject(Auth);
  private router = inject(Router);

  readonly versionTextosLegales = VERSION_TEXTOS_LEGALES;

  nombre = signal('');
  email = signal('');
  clave = signal('');
  confirmarClave = signal('');
  aceptaTerminos = signal(false);
  aceptaRiesgo = signal(false);
  aceptaComercial = signal(false);
  mostrarClave = signal(false);
  enviando = signal(false);
  errorMensaje = signal<string | null>(null);

  // Para no mostrar el error de un campo antes de que la persona lo haya
  // tocado o haya intentado enviar el formulario (evita una pantalla
  // llena de rojo desde el primer caracter escrito).
  private nombreTocado = signal(false);
  private emailTocado = signal(false);
  private claveTocado = signal(false);
  private confirmarClaveTocado = signal(false);
  private intentoEnviar = signal(false);

  // Validez real de cada campo, independiente de si ya se muestra el
  // error en pantalla. El servidor vuelve a validar todo esto: aqui es
  // solo para habilitar el boton y dar una respuesta inmediata.
  private nombreValido = computed(() => {
    const valor = this.nombre().trim();
    // [...valor].length cuenta caracteres reales (puntos de codigo), igual
    // que mb_strlen() en el servidor; .length cuenta unidades UTF-16 y
    // puede dar un numero distinto con emojis u otros caracteres fuera del
    // plano basico.
    const longitud = [...valor].length;
    return longitud >= 2 && longitud <= 150 && !/[\x00-\x1F\x7F]/.test(valor);
  });

  private emailValido = computed(() => {
    const valor = this.email().trim();
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor) && valor.length <= 100;
  });

  private claveValida = computed(() => {
    const valor = this.clave();
    if ([...valor].length < 10) return false;
    // bcrypt trunca a 72 BYTES, no caracteres: un acento o un emoji puede
    // ocupar varios bytes en UTF-8, por eso se mide con TextEncoder y no
    // con .length ni con [...valor].length (que cuentan caracteres, no bytes).
    return new TextEncoder().encode(valor).length <= 72;
  });

  private confirmacionValida = computed(
    () => this.confirmarClave().length > 0 && this.confirmarClave() === this.clave()
  );

  private aceptacionesValidas = computed(() => this.aceptaTerminos() && this.aceptaRiesgo());

  // computed() (y no un metodo normal) porque el resultado depende de
  // varios signals a la vez y el template lo lee en cada deteccion de
  // cambios: computed() memoriza el valor y solo lo vuelve a calcular
  // cuando cambia alguno de los signals de los que depende, en vez de
  // reevaluarse en cada ciclo aunque nada relevante haya cambiado.
  private formularioValido = computed(
    () =>
      this.nombreValido() &&
      this.emailValido() &&
      this.claveValida() &&
      this.confirmacionValida() &&
      this.aceptacionesValidas()
  );

  errorNombre = computed<string | null>(() => {
    if (!this.nombreTocado() && !this.intentoEnviar()) return null;
    return this.nombreValido()
      ? null
      : 'El nombre debe tener entre 2 y 150 caracteres, sin caracteres de control.';
  });

  errorEmail = computed<string | null>(() => {
    if (!this.emailTocado() && !this.intentoEnviar()) return null;
    return this.emailValido() ? null : 'Ingresa un correo electrónico válido.';
  });

  errorClave = computed<string | null>(() => {
    if (!this.claveTocado() && !this.intentoEnviar()) return null;
    if ([...this.clave()].length < 10) return 'La contraseña debe tener al menos 10 caracteres.';
    if (!this.claveValida()) return 'La contraseña es demasiado larga (máximo 72 bytes).';
    return null;
  });

  errorConfirmarClave = computed<string | null>(() => {
    if (!this.confirmarClaveTocado() && !this.intentoEnviar()) return null;
    return this.confirmacionValida() ? null : 'Las contraseñas no coinciden.';
  });

  errorAceptaciones = computed<string | null>(() => {
    if (!this.intentoEnviar()) return null;
    return this.aceptacionesValidas() ? null : 'Debes aceptar los términos y el aviso de riesgo.';
  });

  // Indicador orientativo de fortaleza (longitud + variedad de
  // caracteres). No sustituye el minimo de 10 caracteres que exige el
  // servidor; es solo una ayuda visual para quien se registra.
  fortalezaClave = computed(() => {
    const valor = this.clave();
    let puntos = 0;
    if (valor.length >= 10) puntos++;
    if (valor.length >= 14) puntos++;
    if (/[a-z]/.test(valor) && /[A-Z]/.test(valor)) puntos++;
    if (/[0-9]/.test(valor)) puntos++;
    if (/[^a-zA-Z0-9]/.test(valor)) puntos++;

    if (valor.length === 0) return { nivel: 'vacia' as const, etiqueta: '', porcentaje: 0 };
    if (puntos <= 1) return { nivel: 'debil' as const, etiqueta: 'Débil', porcentaje: 33 };
    if (puntos <= 3) return { nivel: 'media' as const, etiqueta: 'Media', porcentaje: 66 };
    return { nivel: 'fuerte' as const, etiqueta: 'Fuerte', porcentaje: 100 };
  });

  actualizarNombre(valor: string) {
    this.nombre.set(valor);
  }
  tocarNombre() {
    this.nombreTocado.set(true);
  }

  actualizarEmail(valor: string) {
    this.email.set(valor);
  }
  tocarEmail() {
    this.emailTocado.set(true);
  }

  actualizarClave(valor: string) {
    this.clave.set(valor);
  }
  tocarClave() {
    this.claveTocado.set(true);
  }

  actualizarConfirmarClave(valor: string) {
    this.confirmarClave.set(valor);
  }
  tocarConfirmarClave() {
    this.confirmarClaveTocado.set(true);
  }

  alternarMostrarClave() {
    this.mostrarClave.set(!this.mostrarClave());
  }

  alEnviar(evento: Event) {
    evento.preventDefault();
    // Proteccion contra doble envio: si ya hay una peticion en curso, no
    // se hace nada mas (el boton tambien queda deshabilitado mientras
    // tanto, pero esto cubre un doble Enter u otra forma de disparar submit).
    if (this.enviando()) return;

    this.intentoEnviar.set(true);
    if (!this.formularioValido()) return;

    this.enviando.set(true);
    this.errorMensaje.set(null);

    const datos: DatosRegistro = {
      nombre: this.nombre().trim(),
      email: this.email().trim(),
      clave: this.clave(),
      confirmarClave: this.confirmarClave(),
      aceptaciones: {
        terminos: this.aceptaTerminos(),
        riesgo: this.aceptaRiesgo(),
        comercial: this.aceptaComercial(),
      },
    };

    this.auth
      .registro(datos)
      .pipe(
        finalize(() => {
          this.enviando.set(false);
          // La clave nunca se queda en memoria mas tiempo del necesario,
          // tanto si el registro funciono como si fallo.
          this.clave.set('');
          this.confirmarClave.set('');
        })
      )
      .subscribe({
        next: (respuesta) => {
          if (respuesta.Resultado === 'Error') {
            this.errorMensaje.set(respuesta.mensaje ?? 'No se pudo crear la cuenta');
            return;
          }
          this.router.navigateByUrl('/app');
        },
        error: () => {
          this.errorMensaje.set('No se pudo conectar con el servidor');
        },
      });
  }
}
