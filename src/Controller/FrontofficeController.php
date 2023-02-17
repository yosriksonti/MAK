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



class FrontofficeController extends AbstractController
{
    private $passwordHasher;
    private $usr_back ;
    private $client;


    public function __construct(UserPasswordHasherInterface $passwordHasher,HttpClientInterface $client)
    {
        $this->passwordHasher = $passwordHasher;
        $this->client = $client;
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
        $vehicules = $vehiculeRepository->findByModele();
        
        usort($vehicules, function($a, $b){
            return count($a->getFeedback()) < count($b->getFeedback());
        });

        return $this->render('frontoffice/home.html.twig', [
            'agences' => $agenceRepository->findAll(),
            'vehicules' => $vehicules,
        ]);
    }

    /**
     * @Route("/pay", name="pay_index")
     */
    public function payment(PaymentRepository $paymentRepo,LocationRepository $locationRepo): Response
    {
        $user = $this->getUser();
        $payment = new Payment();
        $location = $locationRepo->findBy(["Num" => $_GET['Num']]);
        $payment->setSessionId("  ");
        $payment->setClient($user);
        $payment->setLocation($location[0]);
        $payment->setTotal($_GET['amount']);
        $date = date("m/d/Y");
        $payment->setCreatedOn(new \DateTime($date));
        $payment->setStatus("pending");
        $payment = $paymentRepo->add($payment,true);
        $response = $this->client->request('POST', 'https://api.preprod.konnect.network/api/v1/payments/init-payment', [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'body' => [
                    "receiverWallet" => "600216e6fd5f7e2d78da9bf4",
                    "amount" => $_GET['amount'] * 1000,
                    "selectedPaymentMethod" => "gateway",
                    "firstName" => "Yosri",
                    "lastName" => "Kossontini",
                    "phoneNumber" => "+21658557726",
                    "token" => "TND",
                    "orderId" => $payment->getId(),
                    "successUrl" => "http://127.0.0.1:8000/paymentsuccess/".$payment->getId(),
                    "failUrl" => "http://127.0.0.1:8000/profile?paymentFail=true"
            ]
        ]);
        $content = json_decode($response->getContent(),true);
        print_r($content);
        $payment->setSessionId($content['paymentRef']);
        $payment = $paymentRepo->add($payment,true);
        $this->user = $user;
        return $this->redirect($content['payUrl']);

    }
    /**
     * @Route("/paymentsuccess/{id}", name="pay_success")
     */
    public function paymentSuccess($id,PaymentRepository $paymentRepo,LocationRepository $locationRepo, MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        $response = $this->client->request('GET', 'https://api.preprod.konnect.network/api/v1/payments/'.$id, [
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);
        $content = json_decode($response->getContent(),true);
        $payment = $paymentRepo->findBy(["id" => $content["orderId"]])[0];
        print_r($content);
        if($payment->getStatus() == "pending" && $content["status"] == "pending") {
            $payment->setStatus("paid");
            $payment->setCreatedOn(new \DateTime("now"));
            $paymentRepo->add($payment, true);
            $location = $payment->getLocation();
            $location->setStatus("Confirmée");
            $location->setEtat("Avance Payée");
            $locationRepo->add($location, true);
            $pendingLocations = $locationRepo->findBy(["Vehicule" => $location->getVehicule()->getId(),"Status" => "Non Confirmée"]);
            foreach( $pendingLocations as $loc) {
                if(strtotime($location->getDate_Loc()) >= strtotime($loc->getDate_Loc()) && strtotime($location->getDate_Loc()) <= strtotime($loc->getDate_Retour())) {
                    $loc->setStatus("Annulée");
                    $loc->setEtat("canceled");
                    $locationRepo->add($loc, true);
                }
                if(strtotime($location->getDate_Retour()) >= strtotime($loc->getDate_Loc()) && strtotime($location->getDate_Retour()) <= strtotime($loc->getDate_Retour())) {
                    $loc->setStatus("Annulée");
                    $loc->setEtat("canceled");
                    $locationRepo->add($loc, true);
                }
            }
            $email = (new TemplatedEmail())
            ->from(new Address('w311940@gmail.com', 'Makrent car'))
            ->to($user->getEmail())
            ->subject('Confirmation')
            ->htmlTemplate('email/successful-email.html.twig');

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                
            }

            return $this->redirectToRoute('front_office_profile',["paymentSuccess" => true], Response::HTTP_SEE_OTHER);

        } else {
            $payment->setStatus($content["status"]);
            $payment->setCreatedOn(new \DateTime("now"));
            $paymentRepo->add($payment, true);
            return $this->redirectToRoute('front_office_profile',["paymentFail" => true], Response::HTTP_SEE_OTHER);
        }
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
        $paymentSuccess = isset($_GET['paymentSuccess']);
        $paymentFail = isset($_GET['paymentFail']);
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
            'today' => $today,
            'paymentSuccess' => $paymentSuccess,
            'paymentFail' => $paymentFail
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
        $today = date('Y-m-d');

        if ($form->isSubmitted() && $form->isValid()) {
            $client->setDatePermis(new \DateTime());
            $client->setDateCIN(new \DateTime());
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

            return $this->redirectToRoute('login', $_GET['GET'], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('frontoffice/signup.html.twig', [
            'client' => $client,
            'form' => $form,
            'today' => $today
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
        $today = date('Y-m-d');
        $location = $locationRepo->findBy(array("Num" => $Num));
        $location = $location[0];
        $vehicule = $location->getVehicule();
        $caut = $vehicule->getCaut();
        $park = $vehicule->getPark();
        $amount = 0;
        if(strtotime($park->getDebut_HS())>strtotime($today) || strtotime($park->getFin_HS())<strtotime($today)) {
            $montant = $location->getMontant();
            $amount = ($montant*50)/100;
        } else {
            $amount = $location->getMontant();
        }
        $this->user = $user;
        return $this->render('frontoffice/reservation.html.twig', [
            "reservation" => $location,
            "amount" => $amount
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
        $today = date("d/m/Y");
        $formattedToday = date("m/d/Y");
        $DP = isset($_GET['DP']) ? strtotime($_GET['DP']) : $today;
        $DD = isset($_GET['DD']) ? strtotime($_GET['DD']) : $today;
        $vehicules_raw = $vehiculeRepository->findByModele();
        $vehicules = [];
        $marques = $vehiculeRepository->findByMarque();
        $Mq = [];
        $isset_mq = isset($_GET['Mq']);
        if($isset_mq) {
            foreach($_GET['Mq'] as $mq) {
                $Mq[$mq] = $mq;
            }
        }
        $start = $today;
        $formattedStart = $formattedToday;
        $dispoCarsArray = [];
        $otherCarsArray = [];
        $user = $this->getUser();
        foreach($vehicules_raw as $vehicule_raw) {
            $vehs = $vehiculeRepository->findBy(array("Modele" => $vehicule_raw->getModele()));
            $dispoArray = [];
            $vehDispo = false;
            $push = false;
            $oldId = 0;
            $isMQ = false;
            $countTotal = 0;
            foreach($vehs as $veh){
                if($oldId != $veh->getId()){
                    $oldId = $veh->getId();
                    $start = $today;
                }
                $start = $today;
                $formattedStart = $formattedToday;
                $locations = $locationRepo->findBy(array("Vehicule" => $veh->getId()),array('Date_Loc' => "ASC"));
                $countTotal += count($locations);
                foreach($locations as $location) {
                    if($location->getStatus() != "Non Confirmée" && $location->getStatus() != "Annulée") {
                        $location_DP = strtotime($location->getDate_Loc());
                        $location_DD = strtotime($location->getDate_Retour());
                        $startTime = strtotime($start);

                        $disponibility = new Disponibility();
                        $disponibility->setStart($start);
                        $disponibility->setEnd(date("d/m/Y", strtotime($location->getDate_Loc()." - 1 days")));
                        $formattedDP = $formattedStart;
                        $formattedDD = date("m/d/Y", strtotime($location->getDate_Loc()." - 1 days"));
                        if($DP >= strtotime($formattedDP) && $DP < strtotime($formattedDD) && $DD >= strtotime($formattedDP) && $DD <= strtotime($formattedDD)) {
                            $vehDispo = true;
                        }
                        array_push($dispoArray,$disponibility);
                        $start = date('d/m/Y', strtotime($location->getDate_Retour(). ' + 1 days'));
                        $formattedStart = date('m/d/Y', strtotime($location->getDate_Retour(). ' + 1 days'));
                    }
                }
                $disponibility = new Disponibility();
                $disponibility->setStart($start);
                array_push($dispoArray,$disponibility);
                if($DP >= strtotime($formattedStart)) {
                    $vehDispo = true;
                }
            }
            usort($dispoArray, function($a, $b) {
                $tmpA = DateTime::createFromFormat("d/m/Y", $a->getStart());
                $tmpB = DateTime::createFromFormat("d/m/Y", $b->getStart());
                return $tmpA > $tmpB;
            });
            $filtered =  Disponibility::getUnique($dispoArray);
            $dispoCarsArray[$vehicule_raw->getId()] = $filtered;
            if($countTotal == 0) {
                $vehDispo = true;
            }
            if($isset_mq && !isset($Mq[$veh->getMarque()])) {
                $vehDispo = false;
                $isMQ = false;
            }
            if($vehDispo) {
                if(!$push) {
                    array_push($vehicules,$vehicule_raw);
                    $push = true;
                }
            } else if (isset($Mq[$veh->getMarque()]) || !$isset_mq){
                if(!$push) {
                    array_push($otherCarsArray,$vehicule_raw);
                    $push = true;
                }
            }
            
        }
        $this->user=$user;
        return $this->render('frontoffice/search.html.twig', [
            'agences' => $agenceRepository->findAll(),
            'vehicules' => $vehicules,
            'otherVehicules' => $otherCarsArray,
            'marques' => $marques,
            'Mq' => $Mq,
            'dispo' => $dispoCarsArray,
            'GET' => $_GET
        ]);
    }

    /**
     * @Route("/car/{id}", name="front_office_car")
     */
    public function car(Vehicule $vehicule, FeedbackRepository $feedbackRepository, AgenceRepository $agenceRepository, LocationRepository $locationRepo, VehiculeRepository $vehiculesRepo, Request $request): Response
    {
        if ($this->isGranted('ROLE_MODERATOR')) {
            return $this->redirectToRoute('dashboard_index');
        }
        $today = date('m/d/Y');
        $today_f2 = date("Y-m-d");
        $dispoArray = [];
        $dispo = true;
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
        $locations = [];
        $vehicules = $vehiculesRepo->findBy(array("Modele" => $vehicule->getModele()));
        foreach($vehicules as $veh) {
            $start = $today;
            foreach( $veh->getLocations() as $location) {
                if($location->getStatus() != "Non Confirmée" && $location->getStatus() != "Annulée") {
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
            }
            $disponibility = new Disponibility();
            $disponibility->setStart($start);
            array_push($dispoArray,$disponibility);
        }
        usort($dispoArray, function($a, $b) {
            return strtotime($a->getStart()) - strtotime($b->getStart());
        });
        $filtered =  Disponibility::getUnique($dispoArray);
        $feedbacks = $feedbackRepository->findBy(array('Vehicule' => $vehicule->getId(), "Visible" => true));
        $this->user = $user;
        return $this->render('frontoffice/car.html.twig', [
            'vehicule' => $vehicule,
            'feedbacks' => $feedbacks,
            'agences' => $agenceRepository->findAll(),
            'form' => $form->createView(),
            'today' => $today_f2,
            'dispo' => $filtered
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
            'vehicules' => $vehiculeRepository->findByModele(),
        ]);
    }
    /**
     * @Route("/booking/{id}", name="front_office_booking", methods={"GET", "POST"})
     */
    public function booking( Vehicule $vehicule, LocationRepository $locationRepository,VehiculeRepository $vehiculesRepo, Request $request): Response
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
        $vehicules = $vehiculesRepo->findBy(array("Modele" => $vehicule->getModele()));
        foreach($vehicules as $veh) {
            $dispo = true;
            foreach($veh->getLocations() as $location) { 
                if($location->getStatus() != "Non Confirmée" && $location->getStatus() != "Annulée") {
                    $location_DP = strtotime($location->getDate_Loc());
                    $location_DD = strtotime($location->getDate_Retour());
                    if($location_DP<=$DP && $location_DD>=$DP) {
                        $dispo = false;
                    }
                    if($location_DP<=$DD && $location_DD>=$DD) {
                        $dispo = false;
                    }
                }
                
            }
            if($dispo) {
                $this->user = $user;
                return $this->render('frontoffice/booking.html.twig', [
                    'vehicule' => $veh,
                    'form' => $form->createView(),
                    'GET' => $_GET,
                    'today' => $today,
                    'DP' => date("Y-m-d",$DP),
                    'DD' => date("Y-m-d",$DD),
                    'BS' => $veh->getPark()->getPrixBabySeat(),
                    'STW' => $veh->getPark()->getPrixSTW(),
                    'PD' => $veh->getPark()->getPrixPersonalDriver(),
                    'SD' => $veh->getPark()->getPrixSecondDriver(),
                    'RS' => $veh->getReservoire(),
                ]); 
            }
        }
        if(!$dispo) {
            $this->user = $user;
            return $this->redirectToRoute('front_office_car', ['id' => $vehicule->getId()], Response::HTTP_SEE_OTHER);
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
        $today = date('Y-m-d');
        $caut = $vehicule->getCaut();
        $park = $vehicule->getPark();
        $isBS = strtotime($park->getDebut_HS())>strtotime($today) || strtotime($park->getFin_HS())<strtotime($today);
        if ($form->isSubmitted() ) {
            $location->setVehicule($vehicule);
            $location->setClient($this->getUser());
            $location->setDateRes(new \DateTime());
            $location->setDateLoc(new \DateTime($location->getDate_Loc()));
            $location->setDateRetour(new \DateTime($location->getDate_Retour()));
            $amount = 0;
            $montant = $location->getMontant();
            if($isBS) {
                $amount = ($montant*50)/100;
            } else {
                $amount += $montant;
            }
            $location->setAvance($amount);
            if($form->isValid()) {
                $loc = $locationRepository->add($location, true);
                $date = date('Y-m-d H:i:s');
                $notification = new Notification();
                $notification->setTitle("Nouvelle Reservation.");
                $notification->setBody($user->getEmail()."|".$user->getName()." ".$user->getLastName()." a fait une nouvelle reservation à ".$date);
                $notification->setCreatedOn(new \DateTime($date));
                $notification->setSeen(false);
                $notificationRepo->add($notification,true);
                $this->user = $user;
                if($_POST['METHD'] == "EL") {
                    return $this->redirectToRoute('pay_index',["amount" => $amount,"Num" => $location->getNum()], Response::HTTP_SEE_OTHER);
                } else {
                    return $this->redirectToRoute('front_office_profile',[], Response::HTTP_SEE_OTHER);

                }
            }
        } else {
            if(!isset($_GET['DP']) || empty($_GET['DP']) || !isset($_GET['DD']) || empty($_GET['DD']) || !isset($_GET['AP']) || empty($_GET['AP']) 
            || !isset($_GET['AD']) || empty($_GET['AD']) || !isset($_GET['BS']) || !isset($_GET['STW']) 
            || !isset($_GET['SD']) || !isset($_GET['PD']) ){
                $this->user = $user;
                return $this->redirectToRoute('front_office_car', ['id' => $vehicule->getId()], Response::HTTP_SEE_OTHER);
            } else {
                $agence_d = $agenceRepository->find($_GET['AP']);
                $agence_r = $agenceRepository->find($_GET['AD']);
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
                $prix = $_GET['SD'] ? $prix+$vehicule->getPark()->getPrixSecondDriver() : $prix ;
                if(isset($_GET['Promo'])) {
                    $promo = $promoRepository->findOneBy(['Code' => $_GET['Promo'] ]);
                    if(!empty($promo)) {
                        $prix = $prix - ($prix * $promo->getPourcentage())/100;
                    }
                }
                $prix = $prix * ($interval->days + 1);
                $prix = $_GET['RS'] ? $prix+$vehicule->getReservoire() : $prix;
                $prix = $_GET['STW'] ? $prix+$vehicule->getPark()->getPrixSTW() : $prix ;
                $prix = $_GET['PD'] ? $prix+$vehicule->getPark()->getPrixPersonalDriver() : $prix ;
                $prix += $agence_d->getFrais();
                $prix += $agence_r->getFrais();
                return $this->render('frontoffice/preview.html.twig', [
                    'vehicule' => $vehicule,
                    'form' => $form->createView(),
                    'GET' => $_GET,
                    'agence_d' => $agence_d->getNom(),
                    'agence_r' => $agence_r->getNom(),
                    'today' => $today,
                    'DP' => $DP,
                    'DD' => $DD,
                    'prix' => $prix,
                    'isBS' => $isBS,
                    'frais' => $agence_d->getFrais()+$agence_r->getFrais(),
                    'BS' => $vehicule->getPark()->getPrixBabySeat(),
                    'STW' => $vehicule->getPark()->getPrixSTW(),
                    'PD' => $vehicule->getPark()->getPrixPersonalDriver(),
                    'SD' => $vehicule->getPark()->getPrixSecondDriver(),
                    'RS' => $vehicule->getReservoire(),
                    'Days' => $interval->days + 1,
                ]);  
            }
        }
    }

    
}
