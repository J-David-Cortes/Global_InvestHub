import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { ApiBase } from './api-base';

@Injectable({
  providedIn: 'root',
})
export class BotInversion extends ApiBase {

  protected url = "http://localhost/proyectos/marketplace_bots/Backend/controladores/bot_inversion.php";

  constructor(http: HttpClient) {
    super(http);
  }

}