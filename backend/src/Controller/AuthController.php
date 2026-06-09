<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\LoginRequest;
use App\Dto\RegisterRequest;
use App\Dto\UserResponse;
use App\Entity\User;
use App\Exception\EmailAlreadyExistsException;
use App\Exception\InvalidCredentialsException;
use App\Service\Auth\LoginServiceInterface;
use App\Service\Auth\UserRegistrarInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Kimlik doğrulama uç noktaları.
 *
 * Controller bilerek "ince" tutulmuştur: iş mantığı servislerde, doğrulama
 * DTO'larda; burada yalnızca HTTP <-> servis çevirisi yapılır.
 *
 * #[MapRequestPayload]: JSON gövdeyi DTO'ya dönüştürür ve doğrular. Doğrulama
 * başarısızsa controller'a hiç girilmeden otomatik 422 yanıtı üretilir.
 */
final class AuthController extends AbstractController
{
    public function __construct(
        private readonly UserRegistrarInterface $userRegistrar,
        private readonly LoginServiceInterface $loginService,
    ) {
    }

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(#[MapRequestPayload] RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->userRegistrar->register($request);
        } catch (EmailAlreadyExistsException $e) {
            // Domain hatasını HTTP 409 Conflict'e çeviriyoruz.
            return $this->json(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        return $this->json(UserResponse::fromEntity($user), Response::HTTP_CREATED);
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(#[MapRequestPayload] LoginRequest $request): JsonResponse
    {
        try {
            $token = $this->loginService->login($request);
        } catch (InvalidCredentialsException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json(['token' => $token]);
    }

    /**
     * Giriş yapmış kullanıcının kendi bilgisini döner.
     * Bu uç nokta security.yaml -> access_control ile ROLE_USER'a kısıtlıdır;
     * buraya yalnızca geçerli JWT ile gelinebilir.
     */
    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'Kimlik doğrulanamadı.'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json(UserResponse::fromEntity($user));
    }
}
