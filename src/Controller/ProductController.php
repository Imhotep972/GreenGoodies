<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/produit', name: 'app_gg_produit')]
    public function produit(): Response
    {
        return $this->render('Produit/produit.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }
}
