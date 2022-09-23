<?php

namespace App\Controller;

use App\Entity\Depence;
use App\Form\DepenceType;
use App\Repository\DepenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/depence')]
class DepenceController extends AbstractController
{
    #[Route('/', name: 'depence_index', methods: ['GET'])]
    public function index(DepenceRepository $depenceRepository): Response
    {
        return $this->render('depence/index.html.twig', [
            'depences' => $depenceRepository->findAll(),
            'url' => $_ENV["APP_URL"]
        ]);
    }

    #[Route('/new', name: 'depence_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DepenceRepository $depenceRepository): Response
    {
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
        ]);
    }

    #[Route('/{id}', name: 'depence_show', methods: ['GET'])]
    public function show(Depence $depence): Response
    {
        return $this->render('depence/show.html.twig', [
            'depence' => $depence,
            'url' => $_ENV["APP_URL"]
        ]);
    }

    #[Route('/{id}/edit', name: 'depence_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Depence $depence, DepenceRepository $depenceRepository): Response
    {
        $form = $this->createForm(DepenceType::class, $depence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $depenceRepository->add($depence, true);

            return $this->redirectToRoute('depence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('depence/edit.html.twig', [
            'depence' => $depence,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'depence_delete', methods: ['POST'])]
    public function delete(Request $request, Depence $depence, DepenceRepository $depenceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$depence->getId(), $request->request->get('_token'))) {
            $depenceRepository->remove($depence, true);
        }

        return $this->redirectToRoute('depence_index', [], Response::HTTP_SEE_OTHER);
    }
}
