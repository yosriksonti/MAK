<?php

namespace App\Controller;

use App\Entity\Blacklist;
use App\Form\BlacklistType;
use App\Repository\BlacklistRepository;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back/blacklist')]
class BlacklistController extends AbstractController
{
    #[Route('/', name: 'blacklist_index', methods: ['GET'])]
    public function index(BlacklistRepository $blacklistRepository, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->render('blacklist/index.html.twig', [
            'blacklists' => $blacklistRepository->findAll(),
            'url' => $_ENV["APP_URL"],
            'notifications' => $notifications
        ]);
    }

    #[Route('/new', name: 'blacklist_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BlacklistRepository $blacklistRepository, NotificationRepository $notificationRepo): Response
    {
        $blacklist = new Blacklist();
        $form = $this->createForm(BlacklistType::class, $blacklist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $blacklistRepository->add($blacklist, true);

            return $this->redirectToRoute('blacklist_index', [], Response::HTTP_SEE_OTHER);
        }

        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->renderForm('blacklist/new.html.twig', [
            'blacklist' => $blacklist,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}', name: 'blacklist_show', methods: ['GET'])]
    public function show(Blacklist $blacklist, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->render('blacklist/show.html.twig', [
            'blacklist' => $blacklist,
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}/edit', name: 'blacklist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blacklist $blacklist, BlacklistRepository $blacklistRepository, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        $form = $this->createForm(BlacklistType::class, $blacklist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $blacklistRepository->add($blacklist, true);

            return $this->redirectToRoute('blacklist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('blacklist/edit.html.twig', [
            'blacklist' => $blacklist,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}', name: 'blacklist_delete', methods: ['POST'])]
    public function delete(Request $request, Blacklist $blacklist, BlacklistRepository $blacklistRepository, NotificationRepository $notificationRepo): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blacklist->getId(), $request->request->get('_token'))) {
            $blacklistRepository->remove($blacklist, true);
        }

        return $this->redirectToRoute('blacklist_index', [], Response::HTTP_SEE_OTHER);
    }
}
