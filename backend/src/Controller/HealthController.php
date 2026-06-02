<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Sistem sağlık kontrolü endpoint'i.
 * Backend'in ayakta olduğunu doğrulamak için kullanılır.
 */
final class HealthController extends AbstractController
{
    #[Route('/api/health', name: 'api_health', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'status'    => 'ok',
            'service'   => 'Kurumsal AI Asistan API',
            'env'       => $this->getParameter('kernel.environment'),
            'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ]);
    }
}
