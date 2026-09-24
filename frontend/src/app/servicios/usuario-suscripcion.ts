import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { ApiBase } from './api-base';

@Injectable({
  providedIn: 'root',
})
export class UsuarioSuscripcion extends ApiBase {

  protected url = "http://localhost/proyectos/marketplace_bots/Backend/controladores/usuario_suscripcion.php";

  constructor(http: HttpClient) {
    super(http);
  }

  planActual() {
    return this.http.get(`${this.url}?control=planActual`);
  }
}