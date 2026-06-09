<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Uygulamanın kullanıcı varlığı.
 *
 * Symfony güvenlik sisteminin bir nesneyi "kullanıcı" olarak tanıyabilmesi için
 * iki sözleşmeyi uygular:
 *  - UserInterface: kimlik (getUserIdentifier) ve roller (getRoles)
 *  - PasswordAuthenticatedUserInterface: hash'lenmiş parolaya erişim (getPassword)
 *
 * Tablo adı "users" olarak verildi; çünkü "user" PostgreSQL'de ayrılmış bir kelimedir.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    /**
     * @var list<string> Kullanıcının sahip olduğu roller (JSON olarak saklanır)
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * Argon2/bcrypt ile hash'lenmiş parola. Düz metin parola asla saklanmaz.
     */
    #[ORM\Column]
    private string $password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Symfony'nin kullanıcıyı benzersiz şekilde tanımlamak için kullandığı değer.
     * Burada e-posta adresini kullanıyoruz.
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Her kullanıcı en azından ROLE_USER rolüne sahiptir.
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Bellekte tutulan hassas geçici verileri temizlemek içindir.
     * Düz metin parola tutmadığımız için yapılacak bir şey yok.
     */
    public function eraseCredentials(): void
    {
    }
}
