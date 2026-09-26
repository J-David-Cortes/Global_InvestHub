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

  editarApiKey(idConexion: number, apiKey: string) {
    return this.http.post(`${this.url}?control=editarApiKey&id=${idConexion}`, JSON.stringify({
      api_key: apiKey,
    }));
  }

  consultaSettings() {
    return this.http.get(`${this.url}?control=consultaSettings`);
  }

}