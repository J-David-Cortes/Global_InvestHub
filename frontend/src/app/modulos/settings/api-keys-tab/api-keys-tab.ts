import { Component, signal } from '@angular/core';
import { ApiKey } from '../../../modelos/api-key.model';

@Component({
  selector: 'app-api-keys-tab',
  imports: [],
  templateUrl: './api-keys-tab.html',
  styleUrl: './api-keys-tab.css',
})
export class ApiKeysTab {
  claves = signal<ApiKey[]>([
    { id: 1, nombre: 'MT5 Production', plataforma: 'MT5', fechaCreacion: 'Aug 1, 2026', ultimoUso: 'Just now', claveOculta: 'gih_mt5_sk_...a4f2' },
    { id: 2, nombre: 'LEAN Trading Engine', plataforma: 'LEAN', fechaCreacion: 'Jul 15, 2026', ultimoUso: '2h ago', claveOculta: 'gih_lean_sk_...c8d1' },
    { id: 3, nombre: 'Webhook Integration', plataforma: 'API', fechaCreacion: 'Jul 3, 2026', ultimoUso: '1d ago', claveOculta: 'gih_wh_sk_...b3e9' },
  ]);

  // Guarda el id de la key que se acaba de copiar, para mostrar un mensaje temporal "Copiado ✓"
  idCopiado = signal<number | null>(null);

  copiarClave(clave: ApiKey) {
    // navigator.clipboard es la API nativa del navegador para copiar texto
    navigator.clipboard.writeText(clave.claveOculta);

    this.idCopiado.set(clave.id);
    setTimeout(() => {
      this.idCopiado.set(null);
    }, 1500);
  }

  eliminarClave(id: number) {
    this.claves.update((actuales) => actuales.filter((k) => k.id !== id));
  }

  generarNuevaClave() {
    const nuevoId = Math.max(...this.claves().map((k) => k.id), 0) + 1;
    const nueva: ApiKey = {
      id: nuevoId,
      nombre: `New Key ${nuevoId}`,
      plataforma: 'API',
      fechaCreacion: 'Just now',
      ultimoUso: 'Never',
      claveOculta: `gih_api_sk_...${Math.random().toString(16).slice(2, 6)}`,
    };
    this.claves.update((actuales) => [...actuales, nueva]);
  }
}