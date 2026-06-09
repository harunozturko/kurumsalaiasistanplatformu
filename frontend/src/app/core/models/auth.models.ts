/**
 * Kimlik doğrulama akışında kullanılan tip tanımları.
 * Tek bir yerde tutulması, FE ile BE arasındaki sözleşmeyi nettleştirir.
 */

/** Login ve register isteklerinin gövdesi. */
export interface Credentials {
  email: string;
  password: string;
}

/** /api/login yanıtı: imzalı JWT. */
export interface AuthResponse {
  token: string;
}

/** /api/me ve /api/register yanıtı: kullanıcının güvenli alanları (parola yok). */
export interface User {
  id: number;
  email: string;
  roles: string[];
}
