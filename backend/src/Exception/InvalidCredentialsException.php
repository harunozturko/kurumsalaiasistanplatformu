<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Giriş sırasında e-posta veya parola hatalıysa fırlatılır.
 *
 * Güvenlik gereği "e-posta yok" ile "parola yanlış" durumlarını ayırmayız;
 * her iki halde de aynı genel hatayı veririz (kullanıcı sayımına karşı koruma).
 */
final class InvalidCredentialsException extends \DomainException
{
    public function __construct(string $message = 'E-posta veya parola hatalı.')
    {
        parent::__construct($message);
    }
}
