<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Kayıt (register) isteğinin gövdesi.
 *
 * Controller'a ulaşmadan önce #[MapRequestPayload] sayesinde Symfony bu sınıfı
 * JSON gövdeden oluşturur ve aşağıdaki kuralları otomatik doğrular.
 * Kural ihlalinde controller'a hiç girilmeden 422 (Unprocessable Entity) döner.
 */
final class RegisterRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'E-posta zorunludur.')]
        #[Assert\Email(message: 'Geçerli bir e-posta adresi giriniz.')]
        public string $email = '',

        #[Assert\NotBlank(message: 'Parola zorunludur.')]
        #[Assert\Length(min: 8, minMessage: 'Parola en az {{ limit }} karakter olmalıdır.')]
        public string $password = '',
    ) {
    }
}
