<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/compte', name: 'app_gg_account')]
    public function index(): Response
    {
        return $this->render('User/compte.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/panier', name: 'app_gg_panier')]
    public function panier(): Response
    {
        return $this->render('User/panier.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/connexion', name: 'app_gg_login')]
    public function login(): Response
    {
        return $this->render('User/connexion.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/inscription', name: 'app_gg_register')]
    public function register(): Response
    {
        return $this->render('User/inscription.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route(path: '/deconnexion', name: 'app_gg_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
