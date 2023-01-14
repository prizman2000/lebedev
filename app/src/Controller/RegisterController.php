<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AppController
{
    private EntityManagerInterface $entityManager;
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->hasher = $hasher;
    }

    #[Route('/api/register', name: 'register', methods:['POST'])]
    public function register(Request $request, UserRepository $userRepository): Response
    {
        try {
            $request = $this->transformJsonBody($request);
            if ($userRepository->findOneBy(['email' => $request->get('email')])) {
                return $this->response(
                    'User with this email already exist!',
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            } else {
                $user = new User();
                $user->setEmail($request->get('email'));
                $user->setPassword($this->hasher->hashPassword($user, $request->get('password')));

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return $this->response(
                    'User added successfully'
                );
            }
        } catch (Exception $e) {
            return $this->response(['status' => Response::HTTP_UNPROCESSABLE_ENTITY, 'errors' => $e->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

    }
}
