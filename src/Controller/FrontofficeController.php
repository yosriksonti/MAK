<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Entity\Client;
use App\Entity\Agence;
use App\Entity\Feedback;
use App\Entity\Location;
use App\Entity\Notification;
use App\Entity\Disponibility;
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
use App\Repository\PromoRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontofficeController extends AbstractController
{
    private $passwordHasher;
    private $usr_back ;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function getBack()
    {
        return $this->usr_back;
    }
    /**
     * @Route("/", name="home_index")
     */
    public function index(AgenceRepository $agenceRepository, VehiculeRepository $vehiculeRepository): Response
    {
        if ($this->isGranted('ROLE_MODERATOR')) {
            return $this->redirectToRoute('dashboard_index');
        }
        $vehicules = $vehiculeRepository->findAll();
        
        usort($vehicules, function($a, $b){
            return count($a->getFeedback()) < count($b->getFeedback());
        });

        return $this->render('frontoffice/home.html.twig', [
            'agences' => $agenceRepository->findAll(),
            'vehicules' => $vehicules,
        ]);
    }
    /**
     * @Route("/profile", name="front_office_profile")
     */
    public function profile(FeedbackRepository $feedbackRepository, Request $request): Response
    {
        if ($this->isGranted('ROLE_MODERATOR')) {
            return $this->redirectToRoute('dashboard_index');
        }
        if (!$this->getUser()) {
            $GET = $_GET;
            $GET["route"] = "front_office_profile";
            return $this->redirectToRoute('login', $GET);
        }
        $user = $this->getUser();
        $today = date('Y-m-d');
        $feedback = new Feedback();
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $feedback->setClient($user);
            $feedback->setVehicule(null);
            $feedbackRepository->add($feedback, true);
            $this->user = $user;
            return $this->redirectToRoute('front_office_profile',[], Response::HTTP_SEE_OTHER);
        }
        $this->user = $user;
        return $this->render('frontoffice/profile.html.twig', [
            'form' => $form->createView(),
            'today' => $today
        ]);
    }
    /**
     * @Route("/signup", name="front_office_signup", methods={"GET", "POST"})
     */
    public function signup(Request $request, ClientRepository $clientRepository, NotificationRepository $notificationRepo): Response
    {
        $user = $this->getUser();
        if($user) {
            return $this->redirectToRoute('home_index', [], Response::HTTP_SEE_OTHER);
        }
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $client->setPassword($this->passwordHasher->hashPassword(
                $client,
                $form->get('password')->getData()
            ));

            $date = date('Y-m-d H:i:s');
            $notification = new Notification();
            $notification->setTitle("Nouvelle Client.");
            $notification->setBody($client->getEmail()." a créé un compte en tant que ".$client->getName()." ".$client->getLastName()." à ".$date);
            $notification->setCreatedOn(new \DateTime($date));
            $notification->setSeen(false);
            $notificationRepo->add($notification,true);
            $clientRepository->add($client, true);

            return $this->redirectToRoute('login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('frontoffice/signup.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }
    /**
     * @Route("/profile/edit", name="front_office_profile_edit")
     */
    public function editProfile(Request $request, ClientRepository $clientRepository) : Response
    {
        if ($this->isGranted('ROLE_MODERATOR')) {
            return $this->redirectToRoute('dashboard_index');
        }
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }
        $client = $this->get('security.token_storage')->getToken()->getUser();
        $client = $clientRepository->find(['id' => $client->getId()]);
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);
        if ($form->isSubmitted() ) {
            if($form->isValid())
            $client->setPassword($this->passwordHasher->hashPassword(
                $client,
                $form->get('password')->getData()
            ));

            $clientRepository->add($client, true);

            return $this->redirectToRoute('front_office_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('frontoffice/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/reservation/{Num}", name="front_office_reservation")
     */
    public function reservation($Num, LocationRepository $locationRepo): Response
    {
        $user = $this->getUser();
        if ($this->isGranted('ROLE_MODERATOR')) {
            return $this->redirectToRoute('dashboard_index');
        }
        $location = $locationRepo->findBy(array("Num" => $Num));
        $this->user = $user;
        return $this->render('frontoffice/reservation.html.twig', [
            "reservation" => $location[0]
        ]);
    }

    /**
     * @Route("/search", name="front_office_search", methods={"GET", "POST"})
     */
    public function search(AgenceRepository $agenceRepository, VehiculeRepository $vehiculeRepository, LocationRepository $locationRepo): Response
    {
        if ($this->isGranted('ROLE_MODERATOR')) {
            return $this->redirectToRoute('dashboard_index');
        }
        $today = date("m/d/Y");
        $DP = isset($_GET['DP']) ? strtotime($_GET['DP']) : $today;
        $DD = isset($_GET['DD']) ? strtotime($_GET['DD']) : $today;
        $vehicules_raw = $vehiculeRepository->findAll();
        $vehicules = [];
        $Mq = [];
        $isset_mq = isset($_GET['Mq']);
        if($isset_mq) {
            foreach($_GET['Mq'] as $mq) {
                $Mq[$mq] = $mq;
            }
        }
        $start = $today;
        $dispoCarsArray = [];
        $user = $this->getUser();
        foreach($vehicules_raw as $vehicule_raw) {
            $dispoArray = [];
            $dispo = true;
            $start = $today;
            $locations = $locationRepo->findBy(array("Vehicule" => $vehicule_raw->getId()),array('Date_Loc' => "ASC"));
            foreach($locations as $location) {
                $disponibility = new Disponibility();
                $location_DP = strtotime($location->getDate_Loc());
                $location_DD = strtotime($location->getDate_Retour());
                $disponibility->setStart($start);
                $disponibility->setEnd(date('m/d/Y', strtotime($location->getDate_Loc(). ' - 1 days')));
                if(strtotime($start)<$location_DP) {
                    array_push($dispoArray,$disponibility);
                    $start = $location->getDate_Retour();
                    $start = date('m/d/Y', strtotime($start. ' + 1 days'));
                } else if(strtotime($start)<$location_DD) {
                    $start = $location->getDate_Retour();
                    $start = date('m/d/Y', strtotime($start. ' + 1 days'));
                }
                if($location_DP<=$DP && $location_DD>=$DP) {
                    $dispo = false;
                }
                if($location_DP<=$DD && $location_DD>=$DD ) {
                    $dispo = false;
                }
                if( $location_DD<=$DD && $location_DP>=$DP) {
                    $dispo = false ;
                }
                if($isset_mq && !isset($Mq[$vehicule_raw->getMarque()])) {
                    $dispo = false;
                }
            }
            if($dispo) {
                array_push($vehicules,$vehicule_raw);
                $disponibility = new Disponibility();
                $disponibility->setStart($start);
                array_push($dispoArray,$disponibility);
                $dispoCarsArray[$vehicule_raw->getId()] = $dispoArray;

            }
        }
        $this->user=$user;
        return $this->render('frontoffice/search.html.twig', [
            'agences' => $agenceRepository->findAll(),
            'vehicules' => $vehicules,
            'dispo' => $dispoCarsArray,
            'GET' => $_GET
        ]);
    }

    /**
     * @Route("/car/{id}", name="front_office_car")
     */
    public function car(Vehicule $vehicule, FeedbackRepository $feedbackRepository, AgenceRepository $agenceRepository, LocationRepository $locationRepo, Request $request): Response
    {
        if ($this->isGranted('ROLE_MODERATOR')) {
            return $this->redirectToRoute('dashboard_index');
        }
        $today = date('m/d/Y');
        $today_f2 = date("Y-m-d");
        $dispoArray = [];
        $dispo = true;
        $start = $today;
        $user = $this->getUser();
        $feedback = new Feedback();
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $feedback->setClient($user);
            $feedback->setVehicule($vehicule);
            $feedbackRepository->add($feedback, true);
            $this->user = $user;
            return $this->redirectToRoute('front_office_car', array('id' => $vehicule->getId()), Response::HTTP_SEE_OTHER);
        }
        $locations = $locationRepo->findBy(array("Vehicule" => $vehicule->getId()),array('Date_Loc' => "ASC"));
        foreach( $locations as $location) {
            $disponibility = new Disponibility();
            $location_DP = strtotime($location->getDate_Loc());
            $location_DD = strtotime($location->getDate_Retour());
            $disponibility->setStart($start);
            $disponibility->setEnd(date('m/d/Y', strtotime($location->getDate_Loc(). ' - 1 days')));
            if(strtotime($start)<$location_DP) {
                array_push($dispoArray,$disponibility);
                $start = $location->getDate_Retour();
                $start = date('m/d/Y', strtotime($start. ' + 1 days'));
            } else if(strtotime($start)<$location_DD) {
                $start = $location->getDate_Retour();
                $start = date('m/d/Y', strtotime($start. ' + 1 days'));
            }
        }
        $disponibility = new Disponibility();
        $disponibility->setStart($start);
        array_push($dispoArray,$disponibility);
        $feedbacks = $feedbackRepository->findBy(array('Vehicule' => $vehicule->getId(), "Visible" => true));
        $this->user = $user;
        return $this->render('frontoffice/car.html.twig', [
            'vehicule' => $vehicule,
            'feedbacks' => $feedbacks,
            'agences' => $agenceRepository->findAll(),
            'form' => $form->createView(),
            'today' => $today_f2,
            'dispo' => $dispoArray
        ]);
    }

    /**
     * @Route("/cars", name="front_office_cars")
     */
    public function cars( VehiculeRepository $vehiculeRepository): Response
    {
        if ($this->isGranted('ROLE_MODERATOR')) {
            return $this->redirectToRoute('dashboard_index');
        }
        return $this->render('frontoffice/cars.html.twig', [
            'vehicules' => $vehiculeRepository->findAll(),
        ]);
    }
    /**
     * @Route("/booking/{id}", name="front_office_booking", methods={"GET", "POST"})
     */
    public function booking( Vehicule $vehicule, LocationRepository $locationRepository, Request $request): Response
    {
        if ($this->isGranted('ROLE_MODERATOR')) {
            return $this->redirectToRoute('dashboard_index');
        }
        $user = $this->getUser();
        $today = date('Y-m-d');
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);
        if(!isset($_GET['DP']) || empty($_GET['DP']) || !isset($_GET['DD']) || empty($_GET['DD'])) {
            $this->user = $user;
            return $this->redirectToRoute('front_office_car', ['id' => $vehicule->getId()], Response::HTTP_SEE_OTHER);
        }
        $DP = strtotime($_GET['DP']);
        $DD = strtotime($_GET['DD']);
        $dispo = true;
        foreach($vehicule->getLocations() as $location) {
            $location_DP = strtotime($location->getDate_Loc());
            $location_DD = strtotime($location->getDate_Retour());
            if($location_DP<=$DP && $location_DD>=$DP) {
                $dispo = false;
            }
            if($location_DP<=$DD && $location_DD>=$DD) {
                $dispo = false;
            }
        }
        if(!$dispo) {
            $this->user = $user;
            return $this->redirectToRoute('front_office_car', ['id' => $vehicule->getId()], Response::HTTP_SEE_OTHER);
        } else {
            $this->user = $user;
            return $this->render('frontoffice/booking.html.twig', [
                'vehicule' => $vehicule,
                'form' => $form->createView(),
                'GET' => $_GET,
                'today' => $today,
                'DP' => date("Y-m-d",$DP),
                'DD' => date("Y-m-d",$DD)
            ]);                            
        }
           
        
    }

    /**
     * @Route("/preview/{id}", name="front_office_preview", methods={"GET", "POST"})
     */
    public function preview( Vehicule $vehicule, LocationRepository $locationRepository, AgenceRepository $agenceRepository, PromoRepository $promoRepository,NotificationRepository $notificationRepo, Request $request): Response
    {
        if ($this->isGranted('ROLE_MODERATOR')) {
            return $this->redirectToRoute('dashboard_index');
        }
        if (!$this->getUser()) {
            $GET = $_GET;
            $GET["route"] = "front_office_preview";
            $GET["id"] = $vehicule->getId();
            return $this->redirectToRoute('login', $GET);
        }
        $user = $this->getUser();
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            $location->setVehicule($vehicule);
            $location->setClient($this->getUser());
            if($form->isValid()) {
                $locationRepository->add($location, true);
                $date = date('Y-m-d H:i:s');
                $notification = new Notification();
                $notification->setTitle("Nouvelle Reservation.");
                $notification->setBody($user->getEmail()."|".$user->getName()." ".$user->getLastName()." a fait une nouvelle reservation à ".$date);
                $notification->setCreatedOn(new \DateTime($date));
                $notification->setSeen(false);
                $notificationRepo->add($notification,true);
                $this->user = $user;
                return $this->redirectToRoute('front_office_profile',[], Response::HTTP_SEE_OTHER);
            }
        } else {
            if(!isset($_GET['DP']) || empty($_GET['DP']) || !isset($_GET['DD']) || empty($_GET['DD']) || !isset($_GET['AP']) || empty($_GET['AP']) 
            || !isset($_GET['AD']) || empty($_GET['AD']) || !isset($_GET['BS']) || !isset($_GET['STW']) 
            || !isset($_GET['SD']) || !isset($_GET['PD']) ){
                $this->user = $user;
                return $this->redirectToRoute('front_office_car', ['id' => $vehicule->getId()], Response::HTTP_SEE_OTHER);
            } else {
                $agence_d = $agenceRepository->find($_GET['AP'])->getNom();
                $agence_r = $agenceRepository->find($_GET['AD'])->getNom();
                $DP = strtotime($_GET['DP']);
                $DD = strtotime($_GET['DD']);

                $DP = date("Y-m-d",$DP);
                $DD = date("Y-m-d",$DD);
                
                $today = date('Y-m-d');

                $date1 = new \DateTime($DP);
                $date2 = new \DateTime($DD);
                $interval = $date1->diff($date2);

                    
                $prix = $vehicule->getPrix();
                $prix = $_GET['BS'] ? $prix+$vehicule->getPark()->getPrixBabySeat() : $prix ;
                $prix = $_GET['STW'] ? $prix+$vehicule->getPark()->getPrixSTW() : $prix ;
                $prix = $_GET['PD'] ? $prix+$vehicule->getPark()->getPrixPersonalDriver() : $prix ;
                $prix = $_GET['SD'] ? $prix+$vehicule->getPark()->getPrixSecondDriver() : $prix ;
                if(isset($_GET['Promo'])) {
                    $promo = $promoRepository->findOneBy(['Code' => $_GET['Promo'] ]);
                    if(!empty($promo)) {
                        $prix = $prix - ($prix * $promo->getPourcentage())/100;
                    }
                }
                $prix = $prix * ($interval->days + 1) + $vehicule->getCaut();
                return $this->render('frontoffice/preview.html.twig', [
                    'vehicule' => $vehicule,
                    'form' => $form->createView(),
                    'GET' => $_GET,
                    'agence_d' => $agence_d,
                    'agence_r' => $agence_r,
                    'today' => $today,
                    'DP' => $DP,
                    'DD' => $DD,
                    'prix' => $prix,
                    
                ]);  
            }
        }
    }

    
}
