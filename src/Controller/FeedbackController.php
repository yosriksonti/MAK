<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Form\FeedbackType;
use App\Repository\FeedbackRepository;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/feedback')]
class FeedbackController extends AbstractController
{
    #[Route('/', name: 'feedback_index', methods: ['GET'])]
    public function index(FeedbackRepository $feedbackRepository, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->render('feedback/index.html.twig', [
            'feedbacks' => $feedbackRepository->findAll(),
            'url' => $_ENV["APP_URL"],
            'notifications' => $notifications
        ]);
    }

    #[Route('/new', name: 'feedback_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FeedbackRepository $feedbackRepository, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        $feedback = new Feedback();
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $feedbackRepository->add($feedback, true);

            return $this->redirectToRoute('feedback_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('feedback/new.html.twig', [
            'feedback' => $feedback,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}', name: 'feedback_show', methods: ['GET'])]
    public function show(Feedback $feedback, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->render('feedback/show.html.twig', [
            'feedback' => $feedback,
            'url' => $_ENV["APP_URL"],
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}/edit', name: 'feedback_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Feedback $feedback, FeedbackRepository $feedbackRepository, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $feedbackRepository->add($feedback, true);

            return $this->redirectToRoute('feedback_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('feedback/edit.html.twig', [
            'feedback' => $feedback,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}', name: 'feedback_delete', methods: ['POST'])]
    public function delete(Request $request, Feedback $feedback, FeedbackRepository $feedbackRepository, NotificationRepository $notificationRepo): Response
    {
        if ($this->isCsrfTokenValid('delete'.$feedback->getId(), $request->request->get('_token'))) {
            $feedbackRepository->remove($feedback, true);
        }

        return $this->redirectToRoute('feedback_index', [], Response::HTTP_SEE_OTHER);
    }
}
