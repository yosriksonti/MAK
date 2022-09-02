<?php

namespace App\Controller;

use App\Entity\Park;
use App\Form\ParkType;
use App\Repository\ParkRepository;
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
     * @Route("/", name="app_park_index", methods={"GET"})
     */
    public function index(ParkRepository $parkRepository): Response
    {
        return $this->render('park/index.html.twig', [
            'parks' => $parkRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_park_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ParkRepository $parkRepository): Response
    {
        $park = new Park();
        $form = $this->createForm(ParkType::class, $park);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parkRepository->add($park, true);

            return $this->redirectToRoute('app_park_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('park/new.html.twig', [
            'park' => $park,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_park_show", methods={"GET"})
     */
    public function show(Park $park): Response
    {
        return $this->render('park/show.html.twig', [
            'park' => $park,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_park_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Park $park, ParkRepository $parkRepository): Response
    {
        $form = $this->createForm(ParkType::class, $park);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parkRepository->add($park, true);

            return $this->redirectToRoute('app_park_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('park/edit.html.twig', [
            'park' => $park,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_park_delete", methods={"POST"})
     */
    public function delete(Request $request, Park $park, ParkRepository $parkRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$park->getId(), $request->request->get('_token'))) {
            $parkRepository->remove($park, true);
        }

        return $this->redirectToRoute('app_park_index', [], Response::HTTP_SEE_OTHER);
    }
}
