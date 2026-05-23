<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Product;
use App\Repository\ProductRepository;

#[Route('/cart',name: 'app_gg_cart_')]
final class CartController extends AbstractController
{
    public function __construct(private ProductRepository $productRepository, )
    {
 
    }

    #[Route('/', name:'index')]
    public function index(SessionInterface $session)
    {
        $panier = $session->get('panier',[]);
        $produits = array();
        $prixTotal = 0;
        foreach( $panier  as  $id => $quantite)
        {
            $produits[$id] = $this->productRepository->find($id);
            $produits[$id]->sousTotal = $produits[$id]->getPrix() * $quantite;
            $prixTotal += $produits[$id]->sousTotal;
        }
        return $this->render('Cart/panier.html.twig', [
            'panier' => $panier,
            'produits' => $produits,
            'prixTotal' => $prixTotal,
        ]);
    }

    #[Route('/add/{id}',name: 'add',requirements: ['id' => '\d+'], methods: ['GET'])]
    public function add(int $id, Product $produit, SessionInterface $session)
    {   
        // $product contient les donnees du produit selectionné ou vide
        if ($produit)
        {
            // on recupere le panier de la session si il existe,  sinon on le cree ([])
            $panier = $session->get('panier',[]);
            if (array_key_exists($id,$panier))
                $panier[$id]++ ;                    // si le produit existe, on incremente la quantite
            else
                $panier[$id] = 1;                   // sinon on l'a met a 1
            // on sauvegarde dans la session
            $session->set('panier',$panier);

            // redirection vers le pannier
            return $this->redirectToRoute('app_gg_cart_index');
        }
    }

    #[Route('/remove/{id}',name: 'remove',requirements: ['id' => '\d+'], methods: ['GET'])]
    public function remove(int $id, Product $produit, SessionInterface $session)
    {   
        // $product contient les donnees du produit selectionné ou vide
        if ($produit)
        {
            // on recupere le panier de la session si il existe
            $panier = $session->get('panier');

            if (!empty($panier[$id]))
            { 
                if ($panier[$id] == 1)      // si il n'y a qu'un produit, on le supprime du panier
                    unset($panier[$id]);
                else
                    $panier[$id]--;         // sinon on le decremente
            }   
            // on sauvegarde dans la session
            $session->set('panier',$panier);

            // redirection vers le pannier
            return $this->redirectToRoute('app_gg_cart_index');
        }
    }

    #[Route('/delete/{id}',name: 'delete', methods: ['GET'])]
    public function delete(int $id, Product $produit, SessionInterface $session)
    {   
 
        // $product contient les donnees du produit selectionné ou vide
        if ($produit)
        {
            // on recupere le panier de la session si il existe
            $panier = $session->get('panier');

            // on regarde si le produit est dans le panier, si oui on l'enleve
            if (!empty($panier[$id]))
                unset($panier[$id]);
            // on sauvegarde dans la session
            $session->set('panier',$panier);

            // redirection vers le pannier
            return $this->redirectToRoute('app_gg_cart_index');
        }
    }

    #[Route('/empty',name: 'empty', methods: ['GET'])]
    public function empty(SessionInterface $session)
    {   
        // on supprime le panier de la session
        $panier = $session->remove('panier');

        // redirection vers le pannier
        return $this->redirectToRoute('app_gg_cart_index');
    }
}