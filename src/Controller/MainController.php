<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_gg_accueil')]
    public function index(): Response
    {
        return $this->render('Main/accueil.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

}
