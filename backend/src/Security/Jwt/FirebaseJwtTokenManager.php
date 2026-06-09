<?php

declare(strict_types=1);

namespace App\Security\Jwt;

use App\Security\Exception\InvalidTokenException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * JwtTokenManagerInterface'in firebase/php-jwt (jwt.io) ile gerçeklenmiş hâli.
 *
 * HS256 simetrik algoritması kullanır: token tek bir gizli anahtar (JWT_SECRET)
 * ile imzalanır ve aynı anahtarla doğrulanır. Anahtar yalnızca sunucuda durur.
 */
final class FirebaseJwtTokenManager implements JwtTokenManagerInterface
{
    /**
     * İmzalama algoritması. HS256 = HMAC + SHA-256.
     */
    private const ALGORITHM = 'HS256';

    public function __construct(
        private readonly string $jwtSecret,
        private readonly int $jwtTtl,
    ) {
    }

    public function createToken(UserInterface $user): string
    {
        $issuedAt = time();

        // Payload = token'ın "claims" (iddialar) kısmı. Bu veriler imzalıdır ama
        // şifreli DEĞİLDİR; jwt.io'da herkes okuyabilir. Bu yüzden parola gibi
        // hassas bilgileri buraya koymayız.
        $payload = [
            'sub'   => $user->getUserIdentifier(),   // subject = kullanıcı kimliği (e-posta)
            'roles' => $user->getRoles(),            // yetkilendirme için roller
            'iat'   => $issuedAt,                     // issued at = üretilme zamanı
            'exp'   => $issuedAt + $this->jwtTtl,     // expiration = son geçerlilik zamanı
        ];

        return JWT::encode($payload, $this->jwtSecret, self::ALGORITHM);
    }

    public function decode(string $token): array
    {
        try {
            // Key nesnesi, doğrulamada hangi anahtar ve algoritmanın kullanılacağını
            // belirtir. İmza tutmazsa veya 'exp' geçmişse aşağıdaki exception'lar atılır.
            $decoded = JWT::decode($token, new Key($this->jwtSecret, self::ALGORITHM));
        } catch (ExpiredException $e) {
            throw new InvalidTokenException('Token süresi dolmuş.', previous: $e);
        } catch (SignatureInvalidException $e) {
            throw new InvalidTokenException('Token imzası geçersiz.', previous: $e);
        } catch (\Throwable $e) {
            throw new InvalidTokenException('Token çözümlenemedi.', previous: $e);
        }

        // stdClass -> dizi. (roles JSON dizisi olduğundan zaten PHP dizisi gelir.)
        return (array) $decoded;
    }
}
