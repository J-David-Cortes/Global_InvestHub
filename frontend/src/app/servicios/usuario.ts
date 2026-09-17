import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { ApiBase } from './api-base';

@Injectable({
  providedIn: 'root',
})
export class Usuario extends ApiBase {

  protected url = "http://localhost/proyectos/marketplace_bots/Backend/controladores/usuario.php";

  constructor(http: HttpClient) {
    super(http);
  }

  // Método propio, además de los heredados de ApiBase
  consultaUno() {
    return this.http.get(`${this.url}?control=consultaUno`);
  }
}
