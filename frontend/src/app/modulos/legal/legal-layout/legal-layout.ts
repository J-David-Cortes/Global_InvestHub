import { Component, input } from '@angular/core';
import { RouterLink } from '@angular/router';
import { VERSION_TEXTOS_LEGALES } from '../../../config/legal';

@Component({
  selector: 'app-legal-layout',
  imports: [RouterLink],
  templateUrl: './legal-layout.html',
  styleUrl: './legal-layout.css',
})
export class LegalLayout {
  titulo = input.required<string>();
  readonly version = VERSION_TEXTOS_LEGALES;
}
