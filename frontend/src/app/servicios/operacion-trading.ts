import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { ApiBase } from './api-base';

@Injectable({
  providedIn: 'root',
})
export class OperacionTrading extends ApiBase {

  protected url = "http://localhost/proyectos/marketplace_bots/Backend/controladores/operacion_trading.php";

  constructor(http: HttpClient) {
    super(http);
  }

  resumen() {
    return this.http.get(`${this.url}?control=resumen`);
  }

  abiertas() {
    return this.http.get(`${this.url}?control=abiertas`);
  }

  cerradas() {
    return this.http.get(`${this.url}?control=cerradas`);
  }
}