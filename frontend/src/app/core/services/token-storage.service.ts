import { Injectable } from '@angular/core';

/**
 * Token'ın tarayıcıda nerede ve nasıl saklandığını soyutlayan servis.
 *
 * Tek sorumluluğu vardır (SRP): token'ı oku/yaz/sil. AuthService veya
 * interceptor "localStorage" detayını bilmez; sadece bu servisi kullanır.
 * Yarın saklama yöntemi değişse (örn. sessionStorage), yalnızca burası değişir.
 */
@Injectable({ providedIn: 'root' })
export class TokenStorageService {
  private readonly key = 'auth_token';

  get(): string | null {
    return localStorage.getItem(this.key);
  }

  set(token: string): void {
    localStorage.setItem(this.key, token);
  }

  clear(): void {
    localStorage.removeItem(this.key);
  }

  has(): boolean {
    return this.get() !== null;
  }
}
