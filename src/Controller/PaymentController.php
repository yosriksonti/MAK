<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Form\PaymentType;
use App\Repository\PaymentRepository;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/payment')]
class PaymentController extends AbstractController
{
    #[Route('/', name: 'payment_index', methods: ['GET'])]
    public function index(PaymentRepository $paymentRepository, NotificationRepository $notificationRepo): Response
    {
        $payments = $paymentRepository->findAll();
        if(isset($_GET['from']) && isset($_GET['to']) && !empty($_GET['from']) && !empty($_GET['to'])) {
            $tmp = [];
            foreach($payments as $payment) {
                if($payment->getCreatedOn() >= new \DateTime($_GET['from']) && $payment->getCreatedOn() <= new \DateTime($_GET['to'])) {
                    $tmp[] = $payment;
                }
            }
            $payments = $tmp;
        }

        $pymnts = [];
        foreach($payments as $payment) {
            if(!isset($pymnts[$payment->getCreatedOn()->format('Y-m-d')])) {
                $pymnts[$payment->getCreatedOn()->format('Y-m-d')] = [];
            }
            $pymnts[$payment->getCreatedOn()->format('Y-m-d')][] = $payment;
        }
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->render('payment/index.html.twig', [
            'payments' => $payments,
            'pymnts' => $pymnts,
            'url' => $_ENV["APP_URL"],
            'notifications' => $notifications,
            'GET' => $_GET
        ]);
    }

    #[Route('/new', name: 'payment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PaymentRepository $paymentRepository, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $paymentRepository->add($payment, true);

            return $this->redirectToRoute('payment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('payment/new.html.twig', [
            'payment' => $payment,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}', name: 'payment_show', methods: ['GET'])]
    public function show(Payment $payment, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        return $this->render('payment/show.html.twig', [
            'payment' => $payment,
            'url' => $_ENV["APP_URL"],
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}/edit', name: 'payment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Payment $payment, PaymentRepository $paymentRepository, NotificationRepository $notificationRepo): Response
    {
        $notifications = $notificationRepo->findBy(array(),array('createdOn' => "DESC"));
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $paymentRepository->add($payment, true);

            return $this->redirectToRoute('payment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('payment/edit.html.twig', [
            'payment' => $payment,
            'form' => $form,
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}', name: 'payment_delete', methods: ['POST'])]
    public function delete(Request $request, Payment $payment, PaymentRepository $paymentRepository, NotificationRepository $notificationRepo): Response
    {
        if ($this->isCsrfTokenValid('delete'.$payment->getId(), $request->request->get('_token'))) {
            $paymentRepository->remove($payment, true);
        }

        return $this->redirectToRoute('payment_index', [], Response::HTTP_SEE_OTHER);
    }
}
