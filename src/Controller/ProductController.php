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
   
    #[Route('/show/{id}', name: 'show',requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showProduit(int $id, Product $product)
    {
        $product = $this->productRepository->find($id);

        $description = str_replace(["\r\n","<br>","<br/>"],"\n",$product->getDescription());
        $tabDesc = explode("\n",$description);

        return $this->render('Produit/produit.html.twig', [
            'product' => $product,
            'tabDesc' => $tabDesc,
        ]);
    }

    #[Route('/', name: 'index',)]
    public function showProduits()
    {
        $products = $this->productRepository->findAll();

        return $this->render('Produit/listeproduits.html.twig', [
            'products' => $products,
        ]);
    }
}
