<?php

namespace App\Controller;

use App\Entity\Exercise;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class ExerciseApiController extends AbstractController
{
    private $errors = [];

    private function setError(string $msg)
    {
        $this->errors[] = $msg;
    }
    private function getErrors()
    {
        return $this->errors;
    }

    /**
     * @Route("/api/v1/exercise/add", name="exercise_add", methods={"POST"})
     */
    public function addExercise(Request $request, EntityManagerInterface $entityManagerInterface)
    {
        $data = json_decode($request->getContent());
        if (!isset($data->name)) $this->setError("name is a required property");
        $exercise = new Exercise();

        if (isset($data->name) && !empty(trim($data->name))) {
            $exercise->setName($data->name);
        }

        if (isset($data->description) && !empty(trim($data->description))) {
            $exercise->setDescription($data->description);
        }

        if ($entityManagerInterface->getRepository(Exercise::class)->findOneBy([
            "name" => $exercise->getName()
        ])) $this->setError("{$exercise->getName()} already exists");

        if (empty($this->getErrors())) {
            try {
                $entityManagerInterface->persist($exercise);
                $entityManagerInterface->flush();
                return new JsonResponse("created {$exercise->getName()}");
            } catch (Throwable $th) {
                return new JsonResponse(["errors" => $th->getMessage()]);
            }
        } else {
            return new JsonResponse(["errors" => $this->getErrors()], 400);
        }
    }
}
