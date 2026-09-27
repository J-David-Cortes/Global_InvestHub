import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { ApiBase } from './api-base';

@Injectable({
  providedIn: 'root',
})
export class Broker extends ApiBase {

  protected url = "http://localhost/proyectos/marketplace_bots/Backend/controladores/broker.php";

  constructor(http: HttpClient) {
    super(http);
  }

}
