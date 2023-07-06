<?php

namespace App\Controller;

use App\Entity\Promo;
use App\Form\PromoType;
use App\Repository\PromoRepository;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back/promo')]
class PromoController extends AbstractController
{
    #[Route('/', name: 'promo_index', methods: ['GET'])]
    public function index(PromoRepository $promoRepository, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->render('promo/index.html.twig', [
            'promos' => $promoRepository->findAll(),
            'url' => $_ENV["APP_URL"],
            'notifications' => $notifications
        ]);
    }

    #[Route('/new', name: 'promo_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PromoRepository $promoRepository, NotificationRepository $notificationRepo): Response
    {
        $promo = new Promo();
        $form = $this->createForm(PromoType::class, $promo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $promoRepository->add($promo, true);

            return $this->redirectToRoute('promo_index', [], Response::HTTP_SEE_OTHER);
        }

        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->renderForm('promo/new.html.twig', [
            'promo' => $promo,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}', name: 'promo_show', methods: ['GET'])]
    public function show(Promo $promo, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->render('promo/show.html.twig', [
            'promo' => $promo,
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}/edit', name: 'promo_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Promo $promo, PromoRepository $promoRepository, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        $form = $this->createForm(PromoType::class, $promo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $promoRepository->add($promo, true);

            return $this->redirectToRoute('promo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('promo/edit.html.twig', [
            'promo' => $promo,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}', name: 'promo_delete', methods: ['POST'])]
    public function delete(Request $request, Promo $promo, PromoRepository $promoRepository, NotificationRepository $notificationRepo): Response
    {
        if ($this->isCsrfTokenValid('delete'.$promo->getId(), $request->request->get('_token'))) {
            $promoRepository->remove($promo, true);
        }

        return $this->redirectToRoute('promo_index', [], Response::HTTP_SEE_OTHER);
    }
}
