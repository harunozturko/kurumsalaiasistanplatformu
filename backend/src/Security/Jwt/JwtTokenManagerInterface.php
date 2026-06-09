<?php

declare(strict_types=1);

namespace App\Security\Jwt;

use App\Security\Exception\InvalidTokenException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * JWT üretme ve çözümleme sözleşmesi.
 *
 * Controller ve Authenticator, somut kütüphaneye değil bu arayüze bağımlıdır
 * (Dependency Inversion). Yarın firebase/php-jwt yerine başka bir kütüphaneye
 * geçilse de bu arayüzü uygulayan yeni bir sınıf yazmak yeterli olur.
 */
interface JwtTokenManagerInterface
{
    /**
     * Verilen kullanıcı için imzalı bir JWT üretir.
     */
    public function createToken(UserInterface $user): string;

    /**
     * Token'ı doğrular ve içindeki payload'ı dizi olarak döner.
     *
     * @return array<string, mixed> En azından 'sub' (kullanıcı kimliği) içerir
     *
     * @throws InvalidTokenException Token geçersiz veya süresi dolmuşsa
     */
    public function decode(string $token): array;
}
