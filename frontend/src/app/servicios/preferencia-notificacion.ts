import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { ApiBase } from './api-base';

@Injectable({
    providedIn: 'root',
})
export class PreferenciaNotificacion extends ApiBase {

    protected url = "http://localhost/proyectos/marketplace_bots/Backend/controladores/preferencia_notificacion.php";

    constructor(http: HttpClient) {
        super(http);
    }

    obtener() {
        return this.http.get(`${this.url}?control=obtener`);
    }

    guardar(preferencias: any) {
        return this.http.post(`${this.url}?control=guardar`, JSON.stringify(preferencias));
    }
}