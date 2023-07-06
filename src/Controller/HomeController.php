<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Entity\Client;
use App\Entity\Agence;
use App\Entity\Feedback;
use App\Entity\Location;
use App\Entity\Notification;
use App\Entity\Disponibility;
use App\Entity\Payment;
use App\Entity\Settings;
use App\Form\FeedbackType;
use App\Form\LocationType;
use App\Form\UserType;
use App\Form\ClientType;
use App\Repository\AgenceRepository;
use App\Repository\VehiculeRepository;
use App\Repository\FeedbackRepository;
use App\Repository\LocationRepository;
use App\Repository\ClientRepository;
use App\Repository\NotificationRepository;
use App\Repository\SettingsRepository;
use App\Repository\PaymentRepository;
use App\Repository\PromoRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use \DateTime;


#[Route('/')]
class HomeController extends AbstractController
{
    
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        if($this->getUser()!= null) {
            if(isset($this->getUser()->getRoles()['ROLE_ADMIN']) || isset($this->getUser()->getRoles()['ROLE_MODERATOR'])){
                return $this->redirectToRoute('dashboard_index');
            } else {
                return $this->redirectToRoute('home_index');
            }
        } else {
            return $this->redirectToRoute('home_index');
        }
    }

    
}
