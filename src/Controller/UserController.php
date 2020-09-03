<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserRegistrationValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/api/v1/register", methods={"POST"})
     */
    public function register(Request $request, UserRegistrationValidator $userRegistrationValidator)
    {
        $userData = json_decode($request->getContent());
        $userRegistrationValidator->validate($userData);
        if (!$userRegistrationValidator->isValid()) {
            return $this->json(["errors" => $userRegistrationValidator->getErrors()]);
        }
        return $this->json("ready to go!");
    }
}
