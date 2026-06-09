<?php

declare(strict_types=1);

namespace App\Service\Auth;

use App\Dto\RegisterRequest;
use App\Entity\User;
use App\Exception\EmailAlreadyExistsException;

/**
 * Yeni kullanıcı kaydı sözleşmesi.
 */
interface UserRegistrarInterface
{
    /**
     * Doğrulanmış kayıt isteğinden yeni bir kullanıcı oluşturur ve kaydeder.
     *
     * @throws EmailAlreadyExistsException E-posta zaten kullanımdaysa
     */
    public function register(RegisterRequest $request): User;
}
