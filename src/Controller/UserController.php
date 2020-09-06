<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserRegistrationValidator;
use Exception;
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
        $user = $userRegistrationValidator->validate($userData);
        if ($user) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                return $this->json('user created', 201);
            } catch (Exception $e) {
                return $this->json($e->getMessage());
            }
        }
        return $this->json(["errors" => $userRegistrationValidator->getErrors()]);
    }
}
