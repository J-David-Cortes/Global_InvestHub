import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { ApiBase } from './api-base';

@Injectable({
  providedIn: 'root',
})
export class Suscripcion extends ApiBase {

  protected url = "http://localhost/proyectos/marketplace_bots/Backend/controladores/suscripcion.php";

  constructor(http: HttpClient) {
    super(http);
  }

  listaPlanes() {
    return this.http.get(`${this.url}?control=listaPlanes`);
  }
}