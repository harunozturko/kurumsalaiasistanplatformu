<?php

declare(strict_types=1);

namespace App\Security;

use App\Security\Exception\InvalidTokenException;
use App\Security\Jwt\JwtTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * Stateless (oturumsuz) JWT kimlik doğrulayıcısı.
 *
 * Her korumalı istekte çalışır:
 *  1. supports()  -> "Authorization: Bearer ..." header'ı var mı?
 *  2. authenticate() -> token'ı çöz, içindeki e-postayla kullanıcıyı yükle
 *  3. başarı -> isteğe devam, başarısızlık -> 401 JSON
 */
final class JwtAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    private const PREFIX = 'Bearer ';

    public function __construct(
        private readonly JwtTokenManagerInterface $tokenManager,
    ) {
    }

    /**
     * true  -> bu authenticator isteği üstlenir
     * false -> başka authenticator denenir
     * null  -> bu istek için hiç çalışma (header yoksa public endpoint'ler erişilebilir kalır)
     */
    public function supports(Request $request): ?bool
    {
        $header = $request->headers->get('Authorization', '');

        return $header !== '' && str_starts_with($header, self::PREFIX);
    }

    public function authenticate(Request $request): Passport
    {
        $header = (string) $request->headers->get('Authorization', '');
        $token = substr($header, \strlen(self::PREFIX)); // "Bearer " önekini at

        if ($token === '') {
            throw new CustomUserMessageAuthenticationException('Token bulunamadı.');
        }

        try {
            $payload = $this->tokenManager->decode($token);
        } catch (InvalidTokenException $e) {
            throw new CustomUserMessageAuthenticationException($e->getMessage(), previous: $e);
        }

        $identifier = $payload['sub'] ?? null;
        if (!\is_string($identifier) || $identifier === '') {
            throw new CustomUserMessageAuthenticationException('Token içeriği geçersiz.');
        }

        // SelfValidatingPassport: parola kontrolü yok (token zaten doğrulandı).
        // UserBadge, identifier (e-posta) ile entity provider üzerinden kullanıcıyı yükler.
        return new SelfValidatingPassport(new UserBadge($identifier));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // null döndürmek "isteğin normal akışına devam et" demektir.
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            [
                'error'   => 'Yetkisiz erişim.',
                'message' => $exception->getMessage(),
            ],
            Response::HTTP_UNAUTHORIZED,
        );
    }

    /**
     * Korumalı bir kaynağa hiç token olmadan (kimliksiz) gelindiğinde çağrılır.
     * Bir API olduğumuz için login sayfasına yönlendirmek yerine 401 JSON döneriz.
     */
    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return new JsonResponse(
            ['error' => 'Kimlik doğrulama gerekli.'],
            Response::HTTP_UNAUTHORIZED,
        );
    }
}
