<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\NotificationRepository;
use App\Repository\ClientRepository;
use App\Entity\Client;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Newsletter;
use App\Form\NewsletterType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard_index") 
     */
    public function index(NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        if (!$this->isGranted('ROLE_MODERATOR')) {
            return $this->redirectToRoute('home_index');
        }
        return $this->render('dashboard/home.html.twig', [
            'notifications' => $notifications
        ]);
    }

    #[Route('/newsletter', name: 'newsletter', methods: ['GET', 'POST'])]
    public function newsletter(ClientRepository $clientRepo,Request $request,NotificationRepository $notificationRepo, MailerInterface $mailer): Response
    {
        $newsletter = new Newsletter();
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $clients = $clientRepo->findEmails();
            
            $email = (new TemplatedEmail())
            ->from(new Address('w311940@gmail.com', 'Makrent car'))
            ->to(...$clients)
            ->subject($newsletter->getSubject())
            ->html($newsletter->getBody());
            
            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                
            }
            return $this->redirectToRoute('newsletter', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dashboard/newsletter.html.twig', [
            'newsletter' => $newsletter,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }
}
