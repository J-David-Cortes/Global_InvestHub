import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { ApiBase } from './api-base';

@Injectable({
  providedIn: 'root',
})
export class Modulo extends ApiBase {

  protected url = "http://localhost/proyectos/marketplace_bots/backend/controladores/modulo.php";

  constructor(http: HttpClient) {
    super(http);
  }
}