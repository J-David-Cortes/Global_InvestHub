import { Component, OnInit, inject, signal } from '@angular/core';
import { PreferenciaNotificacion } from '../../../servicios/preferencia-notificacion';
import {
  PreferenciasNotificacion,
  ClavePreferencia,
  ItemNotificacion,
} from '../../../modelos/preferencias-notificacion.model';

@Component({
  selector: 'app-notifications-tab',
  imports: [],
  templateUrl: './notifications-tab.html',
  styleUrl: './notifications-tab.css',
})
export class NotificationsTab implements OnInit {
  private preferenciaService = inject(PreferenciaNotificacion);

  cargando = signal(true);

  preferencias = signal<PreferenciasNotificacion>({
    emailTrades: true,
    emailAlerts: true,
    emailReports: false,
    pushTrades: true,
    pushAlerts: true,
    pushNews: false,
  });

  itemsEmail: ItemNotificacion[] = [
    { etiqueta: 'Trade Executions', descripcion: 'Recibir email cuando se ejecute un trade', clave: 'emailTrades' },
    { etiqueta: 'Price Alerts', descripcion: 'Alertas cuando el precio alcance niveles definidos', clave: 'emailAlerts' },
    { etiqueta: 'Weekly Reports', descripcion: 'Resumen semanal de rendimiento', clave: 'emailReports' },
  ];

  itemsPush: ItemNotificacion[] = [
    { etiqueta: 'Trade Executions', descripcion: 'Notificaciones push para cada trade', clave: 'pushTrades' },
    { etiqueta: 'Risk Alerts', descripcion: 'Alertas de drawdown y límites de riesgo', clave: 'pushAlerts' },
    { etiqueta: 'Market News', descripcion: 'Noticias relevantes para tus pares', clave: 'pushNews' },
  ];

  ngOnInit() {
    this.cargarPreferencias();
  }

  cargarPreferencias() {
    this.cargando.set(true);
    this.preferenciaService.obtener().subscribe({
      next: (respuesta: any) => {
        this.preferencias.set(respuesta);
        this.cargando.set(false);
      },
      error: (err) => {
        console.error('Error al cargar preferencias de notificación:', err);
        this.cargando.set(false);
      },
    });
  }

  alternarPreferencia(clave: ClavePreferencia) {
    // Actualizamos primero en pantalla (respuesta inmediata para el usuario)
    this.preferencias.update((actual) => ({
      ...actual,
      [clave]: !actual[clave],
    }));

    // Y guardamos el objeto completo actualizado en el backend
    this.preferenciaService.guardar(this.preferencias()).subscribe({
      error: (err) => console.error('Error al guardar preferencias:', err),
    });
  }
}