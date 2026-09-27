import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiBase } from './api-base';
import { EstadoBroker, RespuestaEscritura } from '../modelos/usuario-bot.model';

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

  cambiarEstado(idConexion: number, activo: boolean) {
    return this.http.post(`${this.url}?control=cambiarEstado&id=${idConexion}`, JSON.stringify({
      activo: activo,
    }));
  }

  consultaSettings() {
    return this.http.get(`${this.url}?control=consultaSettings`);
  }

  // Flujo Subscribe: ¿el usuario ya tiene algun broker (activo o no)? Si si,
  // ultimoBrokerId es el de su conexion mas reciente, para sugerirlo.
  estadoBroker() {
    return this.http.get<EstadoBroker>(`${this.url}?control=estadoBroker`);
  }

  // Crea una conexion nueva usuario-bot-broker. No se manda fo_usuario (el
  // backend usa el del usuario autenticado) ni fo_pasarela (la pasarela de
  // pago queda para cuando exista login/home). La api_key siempre la escribe
  // el usuario: no se reutiliza ninguna guardada.
  suscribir(fo_bot: number, fo_broker: number, apiKey: string) {
    return this.insertar({
      fo_bot,
      fo_broker,
      api_key: apiKey,
    }) as Observable<RespuestaEscritura>;
  }

  // Variante de suscribir() para el broker "Otro": manda el nombre y el
  // backend lo obtiene o lo crea, junto con la suscripcion, en una sola
  // transaccion (si la suscripcion se rechaza, el broker no queda creado).
  // Nunca se manda fo_broker junto con broker_nuevo: el backend lo rechaza.
  suscribirConBrokerNuevo(fo_bot: number, brokerNuevo: string, apiKey: string) {
    return this.insertar({
      fo_bot,
      broker_nuevo: brokerNuevo,
      api_key: apiKey,
    }) as Observable<RespuestaEscritura>;
  }

}