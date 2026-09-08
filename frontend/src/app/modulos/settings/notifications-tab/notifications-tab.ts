import { Component, signal } from '@angular/core';
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
export class NotificationsTab {
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

  // Recibe CUÁL de las 6 preferencias hay que alternar
  alternarPreferencia(clave: ClavePreferencia) {
    this.preferencias.update((actual) => ({
      ...actual,
      [clave]: !actual[clave],
    }));
  }
}