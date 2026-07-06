<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/produit',name: 'app_product_')]
final class ProductController extends AbstractController
{
    public function __construct(private ProductRepository $productRepository, private EntityManagerInterface $entityManager, )
    {
    }
    
    #[Route('/', name: 'index',)]
    public function showProducts()
    {
        $products = $this->productRepository->findAll();

        return $this->render('Produit/listeproduits.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/show/{id}', name: 'show',requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showProduct(int $id,?Product $product, SessionInterface $session)
    {
        //$product = $this->productRepository->find($id);

        if (empty($product))        // le produit n'existe pas dans la base
        {
            $this->addFlash('product', "Erreur : Le produit n'existe pas.");
            return $this->redirectToRoute('app_product_index');   
        }
        else                        // le produit existe, on affiche les détails voulus
        {
            $description = str_replace(["\r\n","<br>","<br/>"],"\n",$product->getDescription());
            $tabDesc = explode("\n",$description);
            
            //$session->remove('cart');
            return $this->render('Produit/produit.html.twig', [
            'product' => $product,
            'tabDesc' => $tabDesc,
            ]);
        }
    }

}
