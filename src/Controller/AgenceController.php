<?php

namespace App\Controller;

use App\Entity\Agence;
use App\Form\AgenceType;
use App\Repository\AgenceRepository;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back/agence')]
class AgenceController extends AbstractController
{
    #[Route('/', name: 'agence_index', methods: ['GET'])]
    public function index(AgenceRepository $agenceRepository, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));

        return $this->render('agence/index.html.twig', [
            'agences' => $agenceRepository->findAll(),
            'url' => $_ENV["APP_URL"],
            'notifications' => $notifications
        ]);
    }

    #[Route('/new', name: 'agence_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AgenceRepository $agenceRepository,NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        $agence = new Agence();
        $form = $this->createForm(AgenceType::class, $agence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $agenceRepository->add($agence, true);

            return $this->redirectToRoute('agence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('agence/new.html.twig', [
            'agence' => $agence,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}', name: 'agence_show', methods: ['GET'])]
    public function show(Agence $agence, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));

        return $this->render('agence/show.html.twig', [
            'agence' => $agence,
            'url' => $_ENV["APP_URL"],
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}/edit', name: 'agence_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Agence $agence, AgenceRepository $agenceRepository, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));

        $form = $this->createForm(AgenceType::class, $agence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $agenceRepository->add($agence, true);

            return $this->redirectToRoute('agence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('agence/edit.html.twig', [
            'agence' => $agence,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}', name: 'agence_delete', methods: ['POST'])]
    public function delete(Request $request, Agence $agence, AgenceRepository $agenceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$agence->getId(), $request->request->get('_token'))) {
            $agenceRepository->remove($agence, true);
        }

        return $this->redirectToRoute('agence_index', [], Response::HTTP_SEE_OTHER);
    }
}
