import { Routes } from '@angular/router';

import { authGuard } from './core/guards/auth.guard';

/**
 * Uygulama rotaları.
 *
 * - login / register: herkese açık.
 * - dashboard: authGuard ile korunur; token yoksa login'e düşülür.
 * - loadComponent (lazy loading): bileşen sadece o rotaya gidildiğinde indirilir.
 */
export const routes: Routes = [
  {
    path: 'login',
    loadComponent: () => import('./features/auth/login/login').then((m) => m.Login),
  },
  {
    path: 'register',
    loadComponent: () => import('./features/auth/register/register').then((m) => m.Register),
  },
  {
    path: 'dashboard',
    canActivate: [authGuard],
    loadComponent: () => import('./features/dashboard/dashboard').then((m) => m.Dashboard),
  },
  { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
  { path: '**', redirectTo: 'dashboard' },
];
