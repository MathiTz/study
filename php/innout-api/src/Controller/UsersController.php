<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UsersType;
use App\Repository\UsersRepository;
use Doctrine\Instantiator\Exception\ExceptionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/users')]
class UsersController extends AbstractController
{
  #[Route('/', name: 'app_users_index', methods: ['GET'])]
  public function index(UsersRepository $usersRepository): Response
  {
    return $this->json([
      'users' => $usersRepository->findBy(['end_date' => null]),
    ]);
  }

  #[Route('/', name: 'app_users_new', methods: ['POST'])]
  public function new(Request $request, EntityManagerInterface $entityManager): Response
  {
    $bodyContent = json_decode($request->getContent(), true);
    $user = new Users();
    $hashedPassword = password_hash($bodyContent['password'], PASSWORD_DEFAULT);
    $user->setName($bodyContent['name']);
    $user->setPassword($hashedPassword);
    $user->setEmail($bodyContent['email']);
    $user->setStartDate(new \DateTime());
    $user->setIsAdmin($bodyContent['isAdmin']);

    $entityManager->persist($user);
    $entityManager->flush();

    return $this->json([
      'user' => $user,
    ]);
  }

  #[Route('/{id}', name: 'app_users_show', methods: ['GET'])]
  public function show(Users $user): Response
  {
    return $this->json([
      'user' => $user,
    ]);
  }

  #[Route('/{id}', name: 'app_users_edit', methods: ['PUT'])]
  public function edit(Request $request, Users $user, EntityManagerInterface $entityManager): Response
  {
    $bodyContent = json_decode($request->getContent(), true);

    foreach ($bodyContent as $key => $value) {
      switch ($key) {
        case $key === 'name':
          $user->setName($value);
          break;
        case $key === 'email':
          $user->setEmail($value);
          break;
        case $key === 'password':
          $user->setPassword(password_hash($value, PASSWORD_DEFAULT));
          break;
        case $key === 'isAdmin':
          $user->setIsAdmin($value);
          break;
        default:
          break;
      }
    }

    $entityManager->persist($user);
    $entityManager->flush();

    return $this->json([
      'user' => $user,
    ]);
  }

  #[Route('/{id}', name: 'app_users_delete', methods: ['DELETE'])]
  public function delete(Request $request, Users $user, EntityManagerInterface $entityManager): Response
  {
    $user->setEndDate(new \DateTime());
    $entityManager->persist($user);
    $entityManager->flush();

    return $this->json([
      'user' => $user
    ]);
  }
}
