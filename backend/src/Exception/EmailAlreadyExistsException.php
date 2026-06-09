<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Kayıt sırasında e-posta zaten kullanımdaysa fırlatılır.
 *
 * Bilerek saf bir domain exception'ıdır (HTTP'den habersiz). HTTP durum koduna
 * (409 Conflict) dönüştürme işini controller katmanı yapar — böylece iş mantığı
 * web katmanına bağımlı kalmaz (Separation of Concerns).
 */
final class EmailAlreadyExistsException extends \DomainException
{
    public function __construct(string $email)
    {
        parent::__construct(sprintf('"%s" e-posta adresi zaten kayıtlı.', $email));
    }
}
