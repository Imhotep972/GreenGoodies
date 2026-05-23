<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
//se App\Entity\Product;
use App\Repository\ProductRepository;


final class MainController extends AbstractController
{
    #[Route('/', name: 'app_gg_accueil')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('Main/accueil.html.twig', [
            'controller_name' => 'MainController',      
            'products'=> $products,
        ]);
    }

}
