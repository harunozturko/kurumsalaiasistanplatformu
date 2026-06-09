import { Injectable, inject, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';

import { AuthResponse, Credentials, User } from '../models/auth.models';
import { TokenStorageService } from './token-storage.service';

/**
 * Uygulamanın kimlik doğrulama beyni.
 *
 * Sorumlulukları:
 *  - Backend'in /register, /login, /me uçlarıyla konuşmak
 *  - Token saklamayı TokenStorageService'e devretmek (kendisi localStorage bilmez)
 *  - Giriş yapan kullanıcıyı bir signal ile tüm uygulamaya yansıtmak
 *
 * İstekler relative '/api/...' adresine gider; proxy.conf.json bunu nginx'e
 * yönlendirir, dolayısıyla CORS'a gerek kalmaz.
 */
@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly http = inject(HttpClient);
  private readonly tokenStorage = inject(TokenStorageService);

  private readonly apiUrl = '/api';

  /** Giriş yapmış kullanıcı; bileşenler bunu okuyarak arayüzü günceller. */
  private readonly currentUser = signal<User | null>(null);
  readonly user = this.currentUser.asReadonly();

  register(credentials: Credentials): Observable<User> {
    return this.http.post<User>(`${this.apiUrl}/register`, credentials);
  }

  login(credentials: Credentials): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.apiUrl}/login`, credentials).pipe(
      // Başarılı girişte token'ı sakla; sonraki istekler interceptor sayesinde
      // bu token'ı otomatik taşır.
      tap((response) => this.tokenStorage.set(response.token)),
    );
  }

  /** Token'la korunan /api/me'yi çağırır ve dönen kullanıcıyı signal'e yazar. */
  me(): Observable<User> {
    return this.http.get<User>(`${this.apiUrl}/me`).pipe(
      tap((user) => this.currentUser.set(user)),
    );
  }

  logout(): void {
    this.tokenStorage.clear();
    this.currentUser.set(null);
  }

  /** Tarayıcıda saklı bir token var mı? (guard bunu kullanır) */
  isLoggedIn(): boolean {
    return this.tokenStorage.has();
  }
}
