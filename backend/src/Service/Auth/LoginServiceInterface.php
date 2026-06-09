<?php

declare(strict_types=1);

namespace App\Service\Auth;

use App\Dto\LoginRequest;
use App\Exception\InvalidCredentialsException;

/**
 * Giriş (kimlik doğrulama + token üretimi) sözleşmesi.
 */
interface LoginServiceInterface
{
    /**
     * Kimlik bilgilerini doğrular ve geçerliyse imzalı bir JWT döner.
     *
     * @return string İmzalı JWT
     *
     * @throws InvalidCredentialsException E-posta bulunamazsa veya parola hatalıysa
     */
    public function login(LoginRequest $request): string;
}
