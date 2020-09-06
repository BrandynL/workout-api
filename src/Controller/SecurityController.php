<?php

namespace App\Controller;

use App\Service\AuthenticationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/api/v1/auth", name="security")
     */
    public function authenticate(Request $request, AuthenticationService $authenticationService)
    {
        $credentials = json_decode($request->getContent());
        if ($user = $authenticationService->authenticateUserFromRequest($request)) {
            return $this->json(["user" => [
                "email" => $user->getEmail(),
                "displayName" => $user->getDisplayName(),
            ]]);
        } else {
            return $this->json(["errors" => $authenticationService->getErrors()]);
        }
        // check if username or display name provided, or simply try to find a user from the credientials provided ?

        return $this->json($credentials);
    }
}
