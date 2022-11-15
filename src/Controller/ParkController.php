<?php

namespace App\Controller;

use App\Entity\Park;
use App\Form\ParkType;
use App\Repository\ParkRepository;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/park")
 */
class ParkController extends AbstractController
{
    /**
     * @Route("/", name="park_index", methods={"GET"})
     */
    public function index(ParkRepository $parkRepository, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->render('park/index.html.twig', [
            'parks' => $parkRepository->findAll(),
            'notifications' => $notifications
        ]);
    }

    /**
     * @Route("/new", name="park_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ParkRepository $parkRepository, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        $park = new Park();
        $form = $this->createForm(ParkType::class, $park);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parkRepository->add($park, true);

            return $this->redirectToRoute('park_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('park/new.html.twig', [
            'park' => $park,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    /**
     * @Route("/{id}", name="park_show", methods={"GET"})
     */
    public function show(Park $park, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->render('park/show.html.twig', [
            'park' => $park,
            'url' => $_ENV["APP_URL"],
            'notifications' => $notifications
        ]);
    }

    /**
     * @Route("/{id}/edit", name="park_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Park $park, ParkRepository $parkRepository, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        $form = $this->createForm(ParkType::class, $park);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parkRepository->add($park, true);

            return $this->redirectToRoute('park_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('park/edit.html.twig', [
            'park' => $park,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    /**
     * @Route("/{id}", name="park_delete", methods={"POST"})
     */
    public function delete(Request $request, Park $park, ParkRepository $parkRepository, NotificationRepository $notificationRepo): Response
    {
        if ($this->isCsrfTokenValid('delete'.$park->getId(), $request->request->get('_token'))) {
            $parkRepository->remove($park, true);
        }

        return $this->redirectToRoute('park_index', [], Response::HTTP_SEE_OTHER);
    }
}
