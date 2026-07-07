import { Routes } from '@angular/router';
import { Main } from './estructura/main';
import { Dashboard } from './modulos/dashboard/dashboard';
import { Engines } from './modulos/engines/engines';
import { Portfolio } from './modulos/portfolio/portfolio';
import { Settings } from './modulos/settings/settings';
import { Login } from './modulos/login/login';

export const routes: Routes = [
    {
        path: '', component: Main,
        children:
        [
            {path: 'dashboard', component: Dashboard},
            {path: 'engines', component: Engines},
            {path: 'portfolio', component: Portfolio},
            {path: 'settings', component: Settings},
            {path: '', redirectTo: 'dashboard', pathMatch: 'full'}
        ]
    },
    {path: 'login', component: Login},
    {path: '**', redirectTo: 'dashboard'}
];
