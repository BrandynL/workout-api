<?php

namespace App\Controller;

use App\Service\AuthenticationService;
use App\Service\JWTService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/api/v1/auth", name="security")
     */
    public function authenticate(Request $request, AuthenticationService $authenticationService, EntityManagerInterface $entityManager)
    {
        if ($user = $authenticationService->authenticateUserFromRequest($request)) {
            $jwt = JWTService::createJWT($user);
            $user->setToken($jwt);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->json([
                "token" => $jwt,
                "displayName" => $user->getDisplayName(),
                "email" => $user->getDisplayName(),
            ]);
        } else {
            return $this->json(["errors" => $authenticationService->getErrors()], 400);
        }
    }
}
