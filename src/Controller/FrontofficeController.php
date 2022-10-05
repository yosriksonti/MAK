<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Entity\Feedback;
use App\Form\FeedbackType;
use App\Repository\AgenceRepository;
use App\Repository\VehiculeRepository;
use App\Repository\FeedbackRepository;
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
            'form' => $form->createView()
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

    
}
