import { Component, inject } from '@angular/core';
import { Router, RouterLink } from '@angular/router';

@Component({
  selector: 'app-header',
  imports: [RouterLink],
  templateUrl: './header.html',
  styleUrl: './header.css',
})
export class Header {
  private router = inject(Router);

  private rutasSinBoton = ['/app/marketplace'];

  get mostrarBotonEngine(): boolean {
    return !this.rutasSinBoton.some((ruta) => this.router.url.startsWith(ruta));
  }
}