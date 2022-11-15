<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
