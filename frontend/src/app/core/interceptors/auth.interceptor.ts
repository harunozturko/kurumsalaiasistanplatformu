import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { catchError, throwError } from 'rxjs';

import { TokenStorageService } from '../services/token-storage.service';

/**
 * Tüm HTTP isteklerinden geçen functional interceptor.
 *
 * 1. Saklı bir token varsa isteğe "Authorization: Bearer <token>" ekler.
 *    (HttpRequest değişmezdir; bu yüzden clone ile yeni bir istek üretiriz.)
 * 2. Sunucu 401 dönerse (token süresi dolmuş/geçersiz) token'ı temizler ve
 *    kullanıcıyı login'e yönlendirir.
 */
export const authInterceptor: HttpInterceptorFn = (req, next) => {
  const tokenStorage = inject(TokenStorageService);
  const router = inject(Router);

  const token = tokenStorage.get();

  const authReq = token
    ? req.clone({ setHeaders: { Authorization: `Bearer ${token}` } })
    : req;

  return next(authReq).pipe(
    catchError((error) => {
      if (error.status === 401) {
        tokenStorage.clear();
        router.navigate(['/login']);
      }
      return throwError(() => error);
    }),
  );
};
