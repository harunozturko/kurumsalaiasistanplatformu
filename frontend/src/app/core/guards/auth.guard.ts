import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';

import { AuthService } from '../services/auth.service';

/**
 * Korumalı route'ları bekleyen functional guard.
 *
 * Token varsa erişime izin verir (true). Yoksa login sayfasına yönlendiren bir
 * UrlTree döner — bu, "false döndürüp ayrıca yönlendirme yapmak"tan daha temiz
 * ve Angular'ın önerdiği yöntemdir.
 */
export const authGuard: CanActivateFn = () => {
  const authService = inject(AuthService);
  const router = inject(Router);

  if (authService.isLoggedIn()) {
    return true;
  }

  return router.createUrlTree(['/login']);
};
