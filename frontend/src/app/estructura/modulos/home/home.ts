import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import { NavbarPublico } from '../../navbar-publico/navbar-publico';
import { Footer } from '../../footer/footer';

@Component({
  selector: 'app-home',
  imports: [RouterLink, NavbarPublico, Footer],
  templateUrl: './home.html',
  styleUrl: './home.css',
})
export class Home {}