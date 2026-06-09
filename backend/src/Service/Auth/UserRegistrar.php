<?php

declare(strict_types=1);

namespace App\Service\Auth;

use App\Dto\RegisterRequest;
use App\Entity\User;
use App\Exception\EmailAlreadyExistsException;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Kayıt iş mantığı. Tek sorumluluğu: yeni kullanıcı oluşturmak (SRP).
 *
 * Parolayı asla düz metin saklamaz; Symfony'nin yapılandırılmış hash'leyicisiyle
 * (security.yaml -> password_hashers: 'auto') hash'ler.
 */
final class UserRegistrar implements UserRegistrarInterface
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function register(RegisterRequest $request): User
    {
        if ($this->users->findOneByEmail($request->email) !== null) {
            throw new EmailAlreadyExistsException($request->email);
        }

        $user = new User();
        $user->setEmail($request->email);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $request->password),
        );

        $this->users->save($user);

        return $user;
    }
}
