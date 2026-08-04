import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root',
})
export class BotInversion {

  url= "http://localhost/proyectos/marketplace_bots/backend/controladores/bot_inversion.php";

  constructor(private http: HttpClient){};

  consulta(){
    return this.http.get(`${this.url}?control=consulta`)
  }

  insertar(params: any){
    return this.http.post(`${this.url}?control=insetar`, JSON.stringify(params));
  }

  editar(id: number, params: any){
    return this.http.post(`${this.url}?control=editar&id=${id}`, JSON.stringify(params));
  }

  eliminar(id: number){
    return this.http.get(`${this.url}?control=eliminar&id=${id}`);
  }

}
