import { Component, signal } from '@angular/core';
import { ProfileTab } from './profile-tab/profile-tab';
import { SecurityTab } from './security-tab/security-tab';
import { NotificationsTab } from './notifications-tab/notifications-tab';
import { ApiKeysTab } from './api-keys-tab/api-keys-tab';
import { BillingTab } from './billing-tab/billing-tab';

type PestanaSettings = 'profile' | 'security' | 'notifications' | 'apikeys' | 'billing';

interface OpcionPestana {
  id: PestanaSettings;
  etiqueta: string;
  icono: string; // clase de Bootstrap Icons
}

@Component({
  selector: 'app-settings',
  imports: [ProfileTab, SecurityTab, NotificationsTab, ApiKeysTab, BillingTab],
  templateUrl: './settings.html',
  styleUrl: './settings.css',
})
export class Settings {
  pestanas: OpcionPestana[] = [
    { id: 'profile', etiqueta: 'Profile', icono: 'bi-person' },
    { id: 'security', etiqueta: 'Security', icono: 'bi-shield-check' },
    { id: 'notifications', etiqueta: 'Notifications', icono: 'bi-bell' },
    { id: 'apikeys', etiqueta: 'API Keys', icono: 'bi-key' },
    { id: 'billing', etiqueta: 'Billing', icono: 'bi-credit-card' },
  ];

  pestanaActiva = signal<PestanaSettings>('profile');

  seleccionarPestana(id: PestanaSettings) {
    this.pestanaActiva.set(id);
  }
}