<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]   // le pnier n'est accessible qu'un a utilisatuer connecté
#[Route('/panier',name: 'app_cart_')]
final class CartController extends AbstractController
{
    public function __construct(private ProductRepository $productRepository,)
    {
 
    }

    #[Route('/', name:'index')]
    public function index(SessionInterface $session)
    {
        $cart = $session->get('cart',[]);
        $products = [];     // tableau des produits contenu dans le panier
        $totalAmount = 0;   // tableau du total par produit du panier

        foreach( $cart  as  $id => $quantity)
        {
            $products[$id] = $this->productRepository->find($id);
            $products[$id]->sousTotal = $products[$id]->getPrice() * $quantity;
            $totalAmount += $products[$id]->sousTotal;
        }

        return $this->render('Cart/Cart.html.twig', [
            'cart' => $cart,
            'products' => $products,
            'totalamount' => $totalAmount,
        ]);
    }

    #[Route('/add/{id}',name: 'add',requirements: ['id' => '\d+'], methods: ['GET'])]
    public function add(int $id, ?Product $product, SessionInterface $session)
    {   // ajoute 1 au produit selectionné dans le panier ou le cree si il n'existe pas

        //$product = $this->productRepository->find($id);
        // $product contient les donnees du produit selectionné ou vide si il n'existe pas
        if ($product)
        {
            // on recupere le panier de la session si il existe,  sinon on le cree ([])
            $cart = $session->get('cart',[]);
            switch( \array_key_exists($id,$cart)) 
            {
                case true:              // si le produit existe, on incremente la quantite
                    $cart[$id]++ ;      
                    break;
                default:                // sinon on met la quantié à 1
                    $cart[$id] = 1;  
            }

            // on sauvegarde dans la session
            $session->set('cart',$cart);
        }
        else
            $this->addFlash('cart','Erreur le produit n\'existe pas.');

        // redirection vers le pannier
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/remove/{id}',name: 'remove',requirements: ['id' => '\d+'], methods: ['GET'])]
    public function remove(int $id,  ?Product $product,SessionInterface $session)
    {   // enleve 1 au produit selectionné dans le panier

        //$product = $this->productRepository->find($id);
        // $product contient les donnees du produit selectionné ou vide
        if ($product)
        {
            // on recupere le panier dans la session si il existe
            $cart = $session->get('cart');

            if (!empty($cart[$id]))
            { 
                switch ($cart[$id]) {
                    case 1 : 
                        unset($cart[$id]);  // si il n'y a qu'un produit, on le supprime du panier
                        break;
                    default:                // sinon on le decremente
                        $cart[$id]--;
                        break;
                }
            }   

            // on sauvegarde dans la session
            $session->set('cart',$cart);
        }
        else
            $this->addFlash('cart','Erreur le produit n\'existe pas.');

        // redirection vers le pannier
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/delete/{id}',name: 'delete', methods: ['GET'])]
    public function delete(int $id,  ?Product $product,SessionInterface $session)
    {   // supprime le produit du panier
 
        //$product = $this->productRepository->find($id);
        // $product contient les donnees du produit selectionné ou vide
        if ($product)
        {
            // on recupere le panier dans session si il existe
            $cart = $session->get('cart');

            // on regarde si le produit est dans le panier, si oui on l'enleve
            if (!empty($cart[$id]))
                unset($cart[$id]);
            // on sauvegarde dans la session
            $session->set('cart',$cart);
        }
        else
            $this->addFlash('cart','Erreur le produit n\'existe pas.');

        // redirection vers le pannier
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/empty',name: 'empty')]
    public function empty(SessionInterface $session)
    {   // vide le penier

        // on supprime le panier de la session
        $session->remove('cart');
            
        $this->addFlash('cart','Le panier a été vidé.');

        // redirection vers le pannier
        return $this->redirectToRoute('app_cart_index');
    }

}