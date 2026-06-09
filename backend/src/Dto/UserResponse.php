<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\User;

/**
 * Kullanıcı bilgisini dışa açarken kullanılan çıktı DTO'su.
 *
 * Entity'yi doğrudan JSON'a çevirmek yerine bu sınıfı kullanırız; böylece
 * parola gibi hassas alanlar yanlışlıkla bile API yanıtına sızamaz.
 */
final class UserResponse
{
    /**
     * @param list<string> $roles
     */
    public function __construct(
        public int $id,
        public string $email,
        public array $roles,
    ) {
    }

    public static function fromEntity(User $user): self
    {
        return new self(
            id: $user->getId() ?? 0,
            email: $user->getEmail(),
            roles: $user->getRoles(),
        );
    }
}
