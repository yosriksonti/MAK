<?php

namespace App\Controller;

use App\Entity\Settings;
use App\Form\SettingsType;
use App\Repository\SettingsRepository;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/settings')]
class SettingsController extends AbstractController
{
    #[Route('/index', name: 'app_settings_index', methods: ['GET'])]
    public function index(SettingsRepository $settingsRepository): Response
    {
        return $this->redirectToRoute('app_settings_show');

    }

    #[Route('/new', name: 'app_settings_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SettingsRepository $settingsRepository): Response
    {
        if (!$this->isGranted('ROLE_MODERATOR')) {
            return $this->redirectToRoute('home_index');
        }
        $setting = new Settings();
        $form = $this->createForm(SettingsType::class, $setting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $settingsRepository->add($setting, true);

            return $this->redirectToRoute('app_settings_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('settings/new.html.twig', [
            'setting' => $setting,
            'form' => $form,
        ]);
    }

    #[Route('/', name: 'app_settings_show', methods: ['GET'])]
    public function show( SettingsRepository $settingsRepository, NotificationRepository $notificationRepo): Response
    {
        if (!$this->isGranted('ROLE_MODERATOR')) {
            return $this->redirectToRoute('home_index');
        }
        $setting = $settingsRepository->findFirst();
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->render('settings/show.html.twig', [
            'setting' => $setting,
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}/edit', name: 'app_settings_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Settings $setting, SettingsRepository $settingsRepository, NotificationRepository $notificationRepo): Response
    {
        if (!$this->isGranted('ROLE_MODERATOR')) {
            return $this->redirectToRoute('home_index');
        }
        $form = $this->createForm(SettingsType::class, $setting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $settingsRepository->add($setting, true);

            return $this->redirectToRoute('app_settings_index', [], Response::HTTP_SEE_OTHER);
        }
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));

        return $this->renderForm('settings/edit.html.twig', [
            'setting' => $setting,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}', name: 'app_settings_delete', methods: ['POST'])]
    public function delete(Request $request, Settings $setting, SettingsRepository $settingsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$setting->getId(), $request->request->get('_token'))) {
            $settingsRepository->remove($setting, true);
        }

        return $this->redirectToRoute('app_settings_index', [], Response::HTTP_SEE_OTHER);
    }
}
