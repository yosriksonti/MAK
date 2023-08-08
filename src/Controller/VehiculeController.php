<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\VehiculeType;
use App\Repository\VehiculeRepository;
use App\Repository\LocationRepository;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/back/vehicule")
 */
class VehiculeController extends AbstractController
{
    /**
     * @Route("/", name="vehicule_index", methods={"GET"})
     */
    public function index(VehiculeRepository $vehiculeRepository, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        $vehicules = $vehiculeRepository->findAll();
        $dispos = [];
        foreach($vehicules as $vehicule){
            $dispo = true;
            if($vehicule->isDispo() == false){
                $dispo = false;
            } else {
                $locations = $vehicule->getLocations();
                foreach($locations as $location){
                    if($location->getEtat() == "En Cours"){
                        $dispo = false;
                    }
                }
            }
            if($dispo){
                if(isset($dispos[$vehicule->getModele()])){
                    array_push($dispos[$vehicule->getModele()],$vehicule);
                }else{
                    $dispos[$vehicule->getModele()] = [$vehicule];
                }
            }
        }
        return $this->render('vehicule/index.html.twig', [
            'vehicules' => $vehicules,
            'dispos' => $dispos,
            'url' => $_ENV["APP_URL"],
            'notifications' => $notifications
        ]);
    }

    /**
     * @Route("/new", name="vehicule_new", methods={"GET", "POST"})
     */
    public function new(Request $request, VehiculeRepository $vehiculeRepository, NotificationRepository $notificationRepo): Response
    {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vehiculeRepository->add($vehicule, true);

            return $this->redirectToRoute('vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->renderForm('vehicule/new.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    /**
     * @Route("/{id}", name="vehicule_show", methods={"GET"})
     */
    public function show(Vehicule $vehicule, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->render('vehicule/show.html.twig', [
            'vehicule' => $vehicule,
            'url' => $_ENV["APP_URL"],
            'notifications' => $notifications
        ]);
    }

    /**
     * @Route("/{id}/edit", name="vehicule_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Vehicule $vehicule, VehiculeRepository $vehiculeRepository, NotificationRepository $notificationRepo): Response
    {
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vehiculeRepository->add($vehicule, true);        

            return $this->redirectToRoute('vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->renderForm('vehicule/edit.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    /**
     * @Route("/{id}", name="vehicule_delete", methods={"POST"})
     */
    public function delete(Request $request, Vehicule $vehicule, VehiculeRepository $vehiculeRepository, NotificationRepository $notificationRepo): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vehicule->getId(), $request->request->get('_token'))) {
            $vehiculeRepository->remove($vehicule, true);
        }
        return $this->redirectToRoute('vehicule_index', [], Response::HTTP_SEE_OTHER);
        
    }
}
