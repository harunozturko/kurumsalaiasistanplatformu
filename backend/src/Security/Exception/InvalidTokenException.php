<?php

declare(strict_types=1);

namespace App\Security\Exception;

/**
 * JWT çözümlenemediğinde (imza geçersiz, süresi dolmuş, bozuk biçim vb.)
 * token yöneticisi tarafından fırlatılır. Authenticator bunu yakalayıp
 * Symfony'nin kimlik doğrulama hatasına çevirir.
 */
final class InvalidTokenException extends \RuntimeException
{
}
