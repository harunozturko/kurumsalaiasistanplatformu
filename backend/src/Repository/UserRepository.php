<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * User varlığına özel veritabanı sorgularını barındırır.
 *
 * PasswordUpgraderInterface: parola hash algoritması zamanla değişirse
 * (örn. bcrypt -> argon2), Symfony başarılı girişte hash'i otomatik günceller.
 *
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * Kullanıcıyı veritabanına yazar (insert veya update).
     */
    public function save(User $user): void
    {
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }

    /**
     * Başarılı kimlik doğrulamada Symfony, parolayı daha güçlü bir hash'e
     * yükseltmek istediğinde bu metodu çağırır.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Desteklenmeyen kullanıcı tipi "%s".', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->save($user);
    }
}
