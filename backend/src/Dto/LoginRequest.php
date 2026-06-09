<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Giriş (login) isteğinin gövdesi. Sadece biçimsel doğrulama yapar;
 * kimlik bilgisinin doğruluğu LoginService içinde kontrol edilir.
 */
final class LoginRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'E-posta zorunludur.')]
        #[Assert\Email(message: 'Geçerli bir e-posta adresi giriniz.')]
        public string $email = '',

        #[Assert\NotBlank(message: 'Parola zorunludur.')]
        public string $password = '',
    ) {
    }
}
