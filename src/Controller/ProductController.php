<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;
//use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

final class ProductController extends AbstractController
{
    public function __construct(private ProductRepository $productRepository, private EntityManagerInterface $entityManager, )
    {
 
    }
   
    #[Route('/produit/{id}', name: 'app_gg_product',requirements: ['id' => '\d+'], methods: ['GET'])]
    public function produit(int $id): Response
    {
        //$produit= $this->ProductRepository->find($id);

        return $this->render('Produit/produit.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }
}
