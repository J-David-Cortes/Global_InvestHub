import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { ApiBase } from './api-base';

@Injectable({
  providedIn: 'root',
})
export class UsuarioBot extends ApiBase {

  protected url = "http://localhost/proyectos/marketplace_bots/Backend/controladores/usuario_bot.php";

  constructor(http: HttpClient) {
    super(http);
  }

}