import { Component, OnInit, inject, signal } from '@angular/core';
import { Router } from '@angular/router';

import { AuthService } from '../../core/services/auth.service';

/**
 * Korumalı panel.
 *
 * authGuard sayesinde buraya sadece token'ı olan kullanıcılar gelebilir.
 * Açılışta /api/me'yi çağırarak (interceptor token'ı ekler) giriş yapan
 * kullanıcının bilgisini getirir ve profil kartında gösterir.
 */
@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.html',
  styleUrl: './dashboard.scss',
})
export class Dashboard implements OnInit {
  private readonly authService = inject(AuthService);
  private readonly router = inject(Router);

  // AuthService'teki signal'ı doğrudan şablona bağlarız.
  protected readonly user = this.authService.user;
  protected readonly loading = signal(true);
  protected readonly errorMessage = signal<string | null>(null);

  ngOnInit(): void {
    this.authService.me().subscribe({
      next: () => this.loading.set(false),
      error: () => {
        // 401 ise interceptor zaten login'e yönlendirir; diğer hataları gösteririz.
        this.errorMessage.set('Profil bilgisi alınamadı.');
        this.loading.set(false);
      },
    });
  }

  protected logout(): void {
    this.authService.logout();
    this.router.navigate(['/login']);
  }
}
