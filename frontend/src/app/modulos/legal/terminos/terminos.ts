import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import { LegalLayout } from '../legal-layout/legal-layout';

@Component({
  selector: 'app-terminos',
  imports: [LegalLayout, RouterLink],
  templateUrl: './terminos.html',
})
export class Terminos {}
