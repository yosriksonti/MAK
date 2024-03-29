<?php

namespace App\Controller;

use App\Entity\Location;
use App\Form\LocationType;
use App\Repository\LocationRepository;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * @Route("/back/location")
 */
class LocationController extends AbstractController
{
    /**
     * @Route("/", name="location_index", methods={"GET"})
     */
    public function index(LocationRepository $locationRepository, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->render('location/index.html.twig', [
            'locations' => $locationRepository->findAll(),
            'url' => $_ENV["APP_URL"],
            'notifications' => $notifications
        ]);
    }

    /**
     * @Route("/new", name="location_new", methods={"GET", "POST"})
     */
    public function new(Request $request, LocationRepository $locationRepository, NotificationRepository $notificationRepo, MailerInterface $mailer): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $locationRepository->add($location, true);
            if($location->getStatus() == "Confirmée") {
                $email = (new TemplatedEmail())
                ->from(new Address('w311940@gmail.com', 'Makrent car'))
                ->to($location->getClient()->getEmail())
                ->subject('Confirmation')
                ->htmlTemplate('email/successful-email.html.twig');
    
                try {
                    $mailer->send($email);
                } catch (TransportExceptionInterface $e) {
                    
                }
            }  
            return $this->redirectToRoute('location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('location/new.html.twig', [
            'location' => $location,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    /**
     * @Route("/{id}", name="location_show", methods={"GET"})
     */
    public function show(Location $location, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->render('location/show.html.twig', [
            'location' => $location,
            'url' => $_ENV["APP_URL"],
            'notifications' => $notifications
        ]);
    }

    /**
     * @Route("/{id}/edit", name="location_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Location $location, LocationRepository $locationRepository, NotificationRepository $notificationRepo, MailerInterface $mailer): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $locationRepository->add($location, true);
            if($location->getStatus() === "Validée") {
                $user = $location->getClient();
                $email = (new TemplatedEmail())
                ->from(new Address('w311940@gmail.com', 'Makrent car'))
                ->to($user->getEmail())
                ->subject('Location validée! Comment était votre expérience?')
                ->htmlTemplate('email/successful-email.html.twig');

                try {
                    $mailer->send($email);
                } catch (TransportExceptionInterface $e) {
                    
                }
            } else if($location->getStatus() === "Annulée") {
                $user = $location->getClient();
                $email = (new TemplatedEmail())
                ->from(new Address('w311940@gmail.com', 'Makrent car'))
                ->to($user->getEmail())
                ->subject('Location anulée!')
                ->htmlTemplate('email/successful-email.html.twig');

                try {
                    $mailer->send($email);
                } catch (TransportExceptionInterface $e) {
                    
                }
            }
            return $this->redirectToRoute('location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('location/edit.html.twig', [
            'location' => $location,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    /**
     * @Route("/{id}", name="location_delete", methods={"POST"})
     */
    public function delete(Request $request, Location $location, LocationRepository $locationRepository, NotificationRepository $notificationRepo): Response
    {
        if ($this->isCsrfTokenValid('delete'.$location->getId(), $request->request->get('_token'))) {
            $locationRepository->remove($location, true);
        }

        return $this->redirectToRoute('location_index', [], Response::HTTP_SEE_OTHER);
    }
}
