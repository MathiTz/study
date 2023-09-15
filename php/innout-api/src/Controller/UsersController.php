<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api', name: 'api_users')]
class UsersController extends AbstractController
{
  #[Route('/users', name: 'users')]
  public function index(UsersRepository $usersRepository, SerializerInterface $serializer): JsonResponse
  {
    $user = $serializer->serialize(
      $usersRepository->findAll(),
      JsonEncoder::FORMAT,
      [AbstractNormalizer::GROUPS => ['std']]
    );

    return $this->json([
      "data" => json_decode($user, true)
    ]);
  }
}
