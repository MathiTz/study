<?php

namespace App\Controller;

use App\Entity\WorkingHours;
use App\Form\WorkingHoursType;
use App\Repository\WorkingHoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

#[Route('/api/working/hours')]
class WorkingHoursController extends AbstractController
{
  #[Route('', name: 'app_working_hours_index', methods: ['GET'])]
  public function index(WorkingHoursRepository $workingHoursRepository, Request $request): Response
  {
    $userId = $request::createFromGlobals()->get('user_id');

    if (!$userId) {
      throw new NotFoundResourceException('Please insert an user.');
    }

    return $this->json([
      'working_hours' => $workingHoursRepository->findBy(["user_id" => $userId]),
    ]);
  }

  #[Route('', name: 'app_working_hours_new', methods: ['POST'])]
  public function new(Request $request, WorkingHoursRepository $workingHoursRepository, EntityManagerInterface $entityManager): Response
  {
    $actualDate = new \DateTime();
    $userId = $request::createFromGlobals()->get('userId');
    $workingHour = $workingHoursRepository->findOneBy(['user_id' => $userId, 'work_date' => $actualDate]) ?: new WorkingHours();

//    if (!$workingHour->getTime1())
//    if ($workingHour->getTime2())
//    if ($workingHour->getTime3())
//    if ($workingHour->getTime4())
//
//    $entityManager->persist($workingHour);
//    $entityManager->flush();

    return $this->json([
      'working_hour' => $workingHour,
    ]);

  }

  #[Route('/{id}', name: 'app_working_hours_show', methods: ['GET'])]
  public function show(WorkingHours $workingHour): Response
  {
    return $this->render('working_hours/show.html.twig', [
      'working_hour' => $workingHour,
    ]);
  }

  #[Route('/{id}/edit', name: 'app_working_hours_edit', methods: ['GET', 'POST'])]
  public function edit(Request $request, WorkingHours $workingHour, EntityManagerInterface $entityManager): Response
  {
    $form = $this->createForm(WorkingHoursType::class, $workingHour);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->flush();

      return $this->redirectToRoute('app_working_hours_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('working_hours/edit.html.twig', [
      'working_hour' => $workingHour,
      'form' => $form,
    ]);
  }

  #[Route('/{id}', name: 'app_working_hours_delete', methods: ['POST'])]
  public function delete(Request $request, WorkingHours $workingHour, EntityManagerInterface $entityManager): Response
  {
    if ($this->isCsrfTokenValid('delete' . $workingHour->getId(), $request->request->get('_token'))) {
      $entityManager->remove($workingHour);
      $entityManager->flush();
    }

    return $this->redirectToRoute('app_working_hours_index', [], Response::HTTP_SEE_OTHER);
  }
}
