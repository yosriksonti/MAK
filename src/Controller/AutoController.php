<?php

namespace App\Controller;

use App\Entity\Auto;
use App\Entity\Picture;
use App\Form\AutoType;
use App\Repository\AutoRepository;
use App\Repository\LocationRepository;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/back/auto")
 */
class AutoController extends AbstractController
{
    /**
     * @Route("/", name="auto_index", methods={"GET"})
     */
    public function index(AutoRepository $autoRepository, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        $autos = $autoRepository->findAll();
        return $this->render('auto/index.html.twig', [
            'autos' => $autos,
            'url' => $_ENV["APP_URL"],
            'notifications' => $notifications
        ]);
    }

    /**
     * @Route("/new", name="auto_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AutoRepository $autoRepository, NotificationRepository $notificationRepo,EntityManagerInterface $entityManager): Response
    {
        $auto = new Auto();
        $form = $this->createForm(AutoType::class, $auto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $auto->setUpdatedAt(new \DateTime());
            $image = $form->get('image')->getData();
            if($image != null) {
                $image->setAuto($auto);
                $image->setCreatedAt(new \DateTime());
                $image->setUpdatedAt(new \DateTime());
                $entityManager->persist($image);
            }
            $autoRepository->add($auto, true);

            return $this->redirectToRoute('auto_index', [], Response::HTTP_SEE_OTHER);
        }

        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->renderForm('auto/new.html.twig', [
            'auto' => $auto,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    /**
     * @Route("/{id}", name="auto_show", methods={"GET"})
     */
    public function show(Auto $auto, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->render('auto/show.html.twig', [
            'auto' => $auto,
            'url' => $_ENV["APP_URL"],
            'notifications' => $notifications
        ]);
    }

    /**
     * @Route("/{id}/edit", name="auto_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Auto $auto, AutoRepository $autoRepository, NotificationRepository $notificationRepo,EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AutoType::class, $auto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $auto->setUpdatedAt(new \DateTime());
            $image = $form->get('image')->getData();
            if($image != null) {
                $image->setAuto($auto);
                $image->setCreatedAt(new \DateTime());
                $image->setUpdatedAt(new \DateTime());
                $entityManager->persist($image);
            }
            $autoRepository->add($auto, true);        

            return $this->redirectToRoute('auto_index', [], Response::HTTP_SEE_OTHER);
        }

        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->renderForm('auto/edit.html.twig', [
            'auto' => $auto,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    /**
     * @Route("/{id}/edit_picture", name="auto_edit_picture", methods={"GET", "POST"})
     */
    public function editPicture(Request $request, Auto $auto, AutoRepository $autoRepository, NotificationRepository $notificationRepo,EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AutoType::class, $auto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $auto->setUpdatedAt(new \DateTime());
            $image = $form->get('image')->getData();
            if($image != null) {
                $image->setAuto($auto);
                $image->setCreatedAt(new \DateTime());
                $image->setUpdatedAt(new \DateTime());
                $entityManager->persist($image);
            }
            $autoRepository->add($auto, true);        

            return $this->redirectToRoute('auto_show', ['id'=>$auto->getId()], Response::HTTP_SEE_OTHER);
        }

        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->renderForm('auto/edit_picture.html.twig', [
            'auto' => $auto,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    /**
     * @Route("/{id}", name="auto_delete", methods={"POST"})
     */
    public function delete(Request $request, Auto $auto, AutoRepository $autoRepository, NotificationRepository $notificationRepo): Response
    {
        if ($this->isCsrfTokenValid('delete'.$auto->getId(), $request->request->get('_token'))) {
            $autoRepository->remove($auto, true);
        }
        return $this->redirectToRoute('auto_index', [], Response::HTTP_SEE_OTHER);
        
    }
    /**
     * @Route("/picture/{id}", name="auto_delete_picture", methods={"POST"})
     */
    public function deletePicture(Request $request, Picture $picture,EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$picture->getId(), $request->request->get('_token'))) {
            $pictureRepo = $entityManager->getRepository(Picture::class);
            $entityManager->remove($picture, true);
            $entityManager->flush();
            return $this->redirectToRoute('auto_show', ['id' => $picture->getAuto()->getId()], Response::HTTP_SEE_OTHER);

        }
        return $this->redirectToRoute('auto_index', [], Response::HTTP_SEE_OTHER);
        
    }
}
