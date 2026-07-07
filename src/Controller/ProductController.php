<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/produit',name: 'app_product_')]
final class ProductController extends AbstractController
{
    public function __construct( private EntityManagerInterface $entityManager,)
    {
    }
    
    #[Route('/show/{id}', name: 'show',requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showProduct(?Product $product, ProductService $productService,SessionInterface $session)
    {
        if (empty($product))        // le produit n'existe pas dans la base
        {
            $this->addFlash('product', "Erreur : Le produit n'existe pas.");
            return $this->redirectToRoute('app_home');   
        }
        else                        // le produit existe, on affiche les détails voulus
        {
            $cart = $session->get('cart',[]);
            $form = $this->createForm(ProductFormType::class, null,[
                'action' => $this->generateUrl('app_cart_add_from_product'),
                'submitLabel' => (empty($cart[$product->getId()]))? 'Ajouter au panier' : 'Mettre à jour',
                'initialQuantity' =>  (empty($cart[$product->getId()]))? 1 : $cart[$product->getId()]['quantity'],
                'product_id' =>  $product->getId(),
            ]);
        
            $product->setFullDescription($productService->cleanDescription($product));
                        
            //$session->remove('cart');
            return $this->render('Produit/produit.html.twig', [
            'product' => $product,
            'form' => $form,
            ]);
        }
    }

}
