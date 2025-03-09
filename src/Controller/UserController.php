<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    private UserService $userService;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(UserService $userService, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->userService = $userService;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    #[Route('/api/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['numeroTelephonePrincipal'], $data['password'])) {
            return $this->json(['message' => 'Données manquantes'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy([
            'numeroTelephonePrincipal' => $data['numeroTelephonePrincipal']
        ]);

        if ($existingUser) {
            return $this->json(['message' => 'Le numéro de téléphone est déjà utilisé'], JsonResponse::HTTP_CONFLICT);
        }

        // Création de l'utilisateur via le service
        try {
            $user = $this->userService->createUser($data['numeroTelephonePrincipal'], $data['password']);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->json(['message' => 'Utilisateur créé avec succès !'], JsonResponse::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Une erreur est survenue'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérification si JSON est bien formé
        if ($data === null) {
            return $this->json(['message' => 'Invalid JSON format.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Vérification des champs obligatoires
        if (!isset($data['numeroTelephonePrincipal'], $data['password'])) {
            return $this->json(['message' => 'Missing fields.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->json(['message' => 'Login successful (authentication logic required).'], JsonResponse::HTTP_OK);
    }
}
