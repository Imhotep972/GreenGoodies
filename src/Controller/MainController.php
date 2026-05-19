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
        return $this->render('accueil.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
    #[Route('/compte', name: 'app_gg_compte')]
    public function compte(): Response
    {
        return $this->render('compte.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
    #[Route('/panier', name: 'app_gg_panier')]
    public function panier(): Response
    {
        return $this->render('panier.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
    #[Route('/inscription', name: 'app_gg_inscription')]
    public function inscription(): Response
    {
        return $this->render('inscription.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
    #[Route('/connexion', name: 'app_gg_connexion')]
    public function connexion(): Response
    {
        return $this->render('connexion.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
    #[Route('/produit', name: 'app_gg_produit')]
    public function produit(): Response
    {
        return $this->render('produit.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
