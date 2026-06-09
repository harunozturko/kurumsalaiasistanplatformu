import { ApplicationConfig, provideBrowserGlobalErrorListeners } from '@angular/core';
import { provideRouter } from '@angular/router';
import { provideHttpClient, withInterceptors } from '@angular/common/http';

import { routes } from './app.routes';
import { authInterceptor } from './core/interceptors/auth.interceptor';

export const appConfig: ApplicationConfig = {
  providers: [
    provideBrowserGlobalErrorListeners(),
    provideRouter(routes),
    // HttpClient'ı uygulamaya tanıtıyoruz ve her isteğe authInterceptor'ı bağlıyoruz.
    // Böylece tüm HTTP çağrıları otomatik olarak Authorization header'ı kazanır.
    provideHttpClient(withInterceptors([authInterceptor])),
  ],
};
