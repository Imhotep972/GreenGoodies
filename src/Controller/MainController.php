<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    public function __construct(private ProductRepository $productRepository, )
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // $session = $request->getSession();
        $products = $this->productRepository->findAll();
        return $this->render('Main/Accueil.html.twig', [
            'products'=> $products,
        ]);
    }
}
