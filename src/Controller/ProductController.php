<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/produit',name: 'app_gg_product_')]

final class ProductController extends AbstractController
{
    public function __construct(private ProductRepository $productRepository, private EntityManagerInterface $entityManager, )
    {
 
    }
   
    #[Route('/produit/{id}', name: 'show',requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showProduit(int $id, Product $produit)
    {
        $produit = $this->productRepository->find($id);

        $description = str_replace(["\r\n","<br>","<br/>"],"\n",$produit->getDescription());
        $tabDesc = explode("\n",$description);

        return $this->render('Produit/produit.html.twig', [
            'product' => $produit,
            'tabDesc' => $tabDesc,
        ]);
    }

    #[Route('/produit', name: 'index',)]
    public function showProduits()
    {
        $produits = $this->productRepository->findAll();

        return $this->render('Produit/listeproduits.html.twig', [
            'products' => $produits,
        ]);
    }
}
