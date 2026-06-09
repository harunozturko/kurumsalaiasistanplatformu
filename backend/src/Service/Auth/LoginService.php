<?php

declare(strict_types=1);

namespace App\Service\Auth;

use App\Dto\LoginRequest;
use App\Exception\InvalidCredentialsException;
use App\Repository\UserRepository;
use App\Security\Jwt\JwtTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Giriş iş mantığı. Tek sorumluluğu: kimlik bilgisini doğrulayıp token üretmek.
 *
 * Token üretimini kendisi yapmaz; bu işi JwtTokenManagerInterface'e devreder
 * (Dependency Inversion). Böylece "nasıl token üretiliyor" detayı bu sınıfı
 * ilgilendirmez.
 */
final class LoginService implements LoginServiceInterface
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JwtTokenManagerInterface $tokenManager,
    ) {
    }

    public function login(LoginRequest $request): string
    {
        $user = $this->users->findOneByEmail($request->email);

        // Kullanıcı yoksa da parola yanlışsa da aynı hatayı veririz.
        if ($user === null || !$this->passwordHasher->isPasswordValid($user, $request->password)) {
            throw new InvalidCredentialsException();
        }

        return $this->tokenManager->createToken($user);
    }
}
