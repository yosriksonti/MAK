<?php

namespace App\Controller;

use App\Entity\Depence;
use App\Form\DepenceType;
use App\Repository\DepenceRepository;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back/depence')]
class DepenceController extends AbstractController
{
    #[Route('/', name: 'depence_index', methods: ['GET'])]
    public function index(DepenceRepository $depenceRepository, NotificationRepository $notificationRepo): Response
    {
        $depences = $depenceRepository->findAll();
        if(isset($_GET['from']) && isset($_GET['to']) && !empty($_GET['from']) && !empty($_GET['to'])) {
            $tmp = [];
            foreach($depences as $depence) {
                if($depence->getDate() >= new \DateTime($_GET['from']) && $depence->getDate() <= new \DateTime($_GET['to'])) {
                    $tmp[] = $depence;
                }
            }
            $depences = $tmp;
        }
        $dpns = [];
        foreach($depences as $depence) {
            if(!isset($dpns[$depence->getDate()->format('Y-m-d')])) {
                $dpns[$depence->getDate()->format('Y-m-d')] = [];
            }
            $dpns[$depence->getDate()->format('Y-m-d')][] = $depence;
        }
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->render('depence/index.html.twig', [
            'depences' => $depences ,
            'dpns' => $dpns,
            'url' => $_ENV["APP_URL"],
            'notifications' => $notifications
        ]);
    }

    #[Route('/new', name: 'depence_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DepenceRepository $depenceRepository, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        $depence = new Depence();
        $form = $this->createForm(DepenceType::class, $depence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $depenceRepository->add($depence, true);

            return $this->redirectToRoute('depence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('depence/new.html.twig', [
            'depence' => $depence,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}', name: 'depence_show', methods: ['GET'])]
    public function show(Depence $depence, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->render('depence/show.html.twig', [
            'depence' => $depence,
            'url' => $_ENV["APP_URL"],
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}/edit', name: 'depence_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Depence $depence, DepenceRepository $depenceRepository, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        $form = $this->createForm(DepenceType::class, $depence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $depenceRepository->add($depence, true);

            return $this->redirectToRoute('depence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('depence/edit.html.twig', [
            'depence' => $depence,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}', name: 'depence_delete', methods: ['POST'])]
    public function delete(Request $request, Depence $depence, DepenceRepository $depenceRepository, NotificationRepository $notificationRepo): Response
    {
        if ($this->isCsrfTokenValid('delete'.$depence->getId(), $request->request->get('_token'))) {
            $depenceRepository->remove($depence, true);
        }

        return $this->redirectToRoute('depence_index', [], Response::HTTP_SEE_OTHER);
    }
}
