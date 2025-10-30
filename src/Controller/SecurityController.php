<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController
{
    #[Route('/api/login_check', name: 'api_login_check', methods: ['POST'])]
    public function login(): JsonResponse
    {
// Point d'entrée pour l'authentification JWT (gérée par le firewall)
        return new JsonResponse(['message' => 'Login endpoint']);
    }
}
