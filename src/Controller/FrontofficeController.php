<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontofficeController extends AbstractController
{
    /**
     * @Route("/home", name="front_office_index")
     */
    public function index(): Response
    {
        return $this->render('frontoffice/home.html.twig');
    }
}
