<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Entity\Agence;
use App\Entity\Feedback;
use App\Entity\Location;
use App\Form\FeedbackType;
use App\Form\LocationType;
use App\Repository\AgenceRepository;
use App\Repository\VehiculeRepository;
use App\Repository\FeedbackRepository;
use App\Repository\LocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontofficeController extends AbstractController
{
    /**
     * @Route("/home", name="home_index")
     */
    public function index(AgenceRepository $agenceRepository, VehiculeRepository $vehiculeRepository): Response
    {
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
     * @Route("/home/search", name="front_office_search")
     */
    public function search(AgenceRepository $agenceRepository, VehiculeRepository $vehiculeRepository): Response
    {
        $DP = strtotime($_GET['DP']);
        $DD = strtotime($_GET['DD']);
        $vehicules_raw = $vehiculeRepository->findAll();
        $vehicules = [];
        $Mq = [];
        $isset_mq = isset($_GET['Mq']);
        if($isset_mq) {
            foreach($_GET['Mq'] as $mq) {
                $Mq[$mq] = $mq;
            }
        }

        foreach($vehicules_raw as $vehicule_raw) {
            $dispo = true;
            foreach($vehicule_raw->getLocations() as $location) {
                $location_DP = strtotime($location->getDate_Loc());
                $location_DD = strtotime($location->getDate_Retour());
                if($location_DP<=$DP && $location_DD>=$DP) {
                    $dispo = false;
                }
                if($location_DP<=$DD && $location_DD>=$DD) {
                    $dispo = false;
                }
                if($isset_mq && !isset($Mq[$vehicule_raw->getMarque()])) {
                    $dispo = false;
                }
            }
            if($dispo) {
                array_push($vehicules,$vehicule_raw);
            }
        }
        return $this->render('frontoffice/search.html.twig', [
            'agences' => $agenceRepository->findAll(),
            'vehicules' => $vehicules,
            'GET' => $_GET
        ]);
    }

    /**
     * @Route("/home/car/{id}", name="front_office_car", methods={"GET", "POST"})
     */
    public function car(Vehicule $vehicule, FeedbackRepository $feedbackRepository, AgenceRepository $agenceRepository, Request $request): Response
    {
        $today = date('Y-m-d');

        $feedback = new Feedback();
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $feedbackRepository->add($feedback, true);
            return $this->redirectToRoute('front_office_car', ['id' => $feedback->getVehicule()->getId()], Response::HTTP_SEE_OTHER);
        }

        $feedbacks = $feedbackRepository->findBy(array('Vehicule' => $vehicule->getId(), "Visible" => true));
        
        return $this->render('frontoffice/car.html.twig', [
            'vehicule' => $vehicule,
            'feedbacks' => $feedbacks,
            'agences' => $agenceRepository->findAll(),
            'form' => $form->createView(),
            'today' => $today
        ]);
    }

    /**
     * @Route("/home/cars", name="front_office_cars")
     */
    public function cars( VehiculeRepository $vehiculeRepository): Response
    {
        return $this->render('frontoffice/cars.html.twig', [
            'vehicules' => $vehiculeRepository->findAll(),
        ]);
    }
    /**
     * @Route("/home/booking/{id}", name="front_office_booking", methods={"GET", "POST"})
     */
    public function booking( Vehicule $vehicule, LocationRepository $locationRepository, Request $request): Response
    {
            $today = date('Y-m-d');
            $location = new Location();
            $form = $this->createForm(LocationType::class, $location);
            $form->handleRequest($request);
            if(!isset($_GET['DP']) || empty($_GET['DP']) || !isset($_GET['DD']) || empty($_GET['DD'])) {
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
                return $this->redirectToRoute('front_office_car', ['id' => $vehicule->getId()], Response::HTTP_SEE_OTHER);
            } else {
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
     * @Route("/home/preview/{id}", name="front_office_preview", methods={"GET", "POST"})
     */
    public function preview( Vehicule $vehicule, LocationRepository $locationRepository, AgenceRepository $agenceRepository, Request $request): Response
    {
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            if($form->isValid()) {
                $locationRepository->add($location, true);
                return $this->redirectToRoute('front_office_car', ['id' => $vehicule->getId()], Response::HTTP_SEE_OTHER);
            }
        } else {
            if(!isset($_GET['DP']) || empty($_GET['DP']) || !isset($_GET['DD']) || empty($_GET['DD']) || !isset($_GET['AP']) || empty($_GET['AP']) 
            || !isset($_GET['AD']) || empty($_GET['AD']) || !isset($_GET['BS']) || !isset($_GET['STW']) 
            || !isset($_GET['SD']) || !isset($_GET['PD']) ){
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
                $prix = $prix * $interval->days + $vehicule->getCaut();
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
