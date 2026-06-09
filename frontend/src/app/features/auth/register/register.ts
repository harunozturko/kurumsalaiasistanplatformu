import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';

import { AuthService } from '../../../core/services/auth.service';

/**
 * Kayıt sayfası.
 *
 * Login ile aynı deseni izler. Parola kuralı (min 8) backend'deki doğrulama ile
 * eşleşir; böylece kullanıcı sunucuya gitmeden hatayı görür. Başarılı kayıttan
 * sonra giriş sayfasına yönlendirir.
 */
@Component({
  selector: 'app-register',
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './register.html',
  styleUrl: './register.scss',
})
export class Register {
  private readonly fb = inject(FormBuilder);
  private readonly authService = inject(AuthService);
  private readonly router = inject(Router);

  protected readonly loading = signal(false);
  protected readonly errorMessage = signal<string | null>(null);

  protected readonly form = this.fb.nonNullable.group({
    email: ['', [Validators.required, Validators.email]],
    password: ['', [Validators.required, Validators.minLength(8)]],
  });

  protected submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.loading.set(true);
    this.errorMessage.set(null);

    this.authService.register(this.form.getRawValue()).subscribe({
      next: () => this.router.navigate(['/login']),
      error: (err) => {
        this.errorMessage.set(err?.error?.error ?? 'Kayıt başarısız. Lütfen tekrar deneyin.');
        this.loading.set(false);
      },
    });
  }
}
