import { Component, OnInit, inject, signal } from '@angular/core';
import { UsuarioBot } from '../../../servicios/usuario-bot';
import { ConexionBroker } from '../../../modelos/conexion-broker.model';

@Component({
  selector: 'app-api-keys-tab',
  imports: [],
  templateUrl: './api-keys-tab.html',
  styleUrl: './api-keys-tab.css',
})
export class ApiKeysTab implements OnInit {
  private usuarioBotService = inject(UsuarioBot);

  cargando = signal(true);
  conexiones = signal<ConexionBroker[]>([]);

  valoresEditados = signal<Record<number, string>>({});

  guardadoExitosoId = signal<number | null>(null);

  ngOnInit() {
    this.cargarConexiones();
  }

  cargarConexiones() {
    this.cargando.set(true);
    this.usuarioBotService.consultaSettings().subscribe({
      next: (respuesta: any) => {
        this.conexiones.set(respuesta);
        this.cargando.set(false);
      },
      error: (err) => {
        console.error('Error al cargar las conexiones de broker:', err);
        this.cargando.set(false);
      },
    });
  }

  valorMostrado(conexion: ConexionBroker): string {
    const editado = this.valoresEditados()[conexion.id];
    return editado !== undefined ? editado : conexion.apiKey;
  }

  actualizarValorEditado(idConexion: number, valor: string) {
    this.valoresEditados.update((actual) => ({ ...actual, [idConexion]: valor }));
  }

  guardarApiKey(conexion: ConexionBroker) {
    const nuevaClave = this.valorMostrado(conexion);

    this.usuarioBotService.editarApiKey(conexion.id, nuevaClave).subscribe({
      next: (respuesta: any) => {
        if (respuesta.Resultado === 'OK') {
          this.conexiones.update((lista) =>
            lista.map((c) => (c.id === conexion.id ? { ...c, apiKey: nuevaClave } : c))
          );
          this.guardadoExitosoId.set(conexion.id);
          setTimeout(() => this.guardadoExitosoId.set(null), 2000);
        } else {
          console.error('Error al guardar la api key:', respuesta.mensaje);
        }
      },
      error: (err) => console.error('Error al guardar la api key:', err),
    });
  }
}