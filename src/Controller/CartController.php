<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(SessionInterface $session, CartService $cartService)
    {
        $cart = $session->get('cart',[]);
        $totalAmount = $cartService->getTotalCart();   // montant totale du panier

        return $this->render('Cart/Cart.html.twig', [
            'cart' => $cart,
            'totalamount' => $totalAmount,
        ]);
    }

    #[Route('/add/{id}',name: 'add',requirements: ['id' => '\d+'], methods: ['POST'])]
    public function add(?Product $product,  CartService $cartService, Request $request)
    {   // ajoute 1 au produit selectionné dans le panier ou le cree si il n'existe pas
        if (!$this->isCsrfTokenValid('app_cart_add', $request->request->get('_token'))) 
        {
            throw $this->createAccessDeniedException();
        }
        else
        {
            $result = $cartService->addQuantity($product);
            $this->addFlash($result['statut'],$result['message']);

            // redirection vers le pannier
            return $this->redirectToRoute('app_cart_index');
        }
    }

    #[Route('/remove/{id}',name: 'remove',requirements: ['id' => '\d+'], methods: ['POST'])]
    public function remove(?Product $product, CartService $cartService,Request $request)
    {   // enleve 1 au produit selectionné dans le panier
        if (!$this->isCsrfTokenValid('app_cart_remove', $request->request->get('_token'))) 
        {
            throw $this->createAccessDeniedException();
        }
        else
        {
            $result = $cartService->removeQuantity($product);
            $this->addFlash($result['statut'],$result['message']);

            // redirection vers le pannier
            return $this->redirectToRoute('app_cart_index');
        }
    }

    #[Route('/delete/{id}',name: 'delete', methods: ['POST'])]
    public function delete(?Product $product, CartService $cartService,Request $request)
    {   // supprime le produit du panier
        if (!$this->isCsrfTokenValid('app_cart_delete', $request->request->get('_token'))) 
        {
            throw $this->createAccessDeniedException();
        }
        else
        {
            $result = $cartService->deleteProduct($product,);
            $this->addFlash($result['statut'],$result['message']);

            // redirection vers le pannier
            return $this->redirectToRoute('app_cart_index');
        }
    }

    #[Route('/empty',name: 'empty',methods: ['POST'])]
    public function empty(CartService $cartService,Request $request)
    {   // vide le penier
        if (!$this->isCsrfTokenValid('app_cart_empty', $request->request->get('_token'))) 
        {
            throw $this->createAccessDeniedException();
        }
        else
        {
            $result = $cartService->emptyCart();
            $this->addFlash($result['statut'],$result['message']);

            // redirection vers le pannier
            return $this->redirectToRoute('app_cart_index');
        }
    }

    #[route('/generate',name:'generate',methods: ['POST'])]
    public function generate(SessionInterface $session, Request $request, CartService $cartService)
    {
        // on recupere le panier depuis la session
        if (!$this->isCsrfTokenValid('app_cart_generate', $request->request->get('_token'))) 
        {
            throw $this->createAccessDeniedException();
        }
        else
        {
            $result = $cartService->generateOrder();
            $this->addFlash($result['statut'],$result['message']);
        }    
  

        // erreurs on reaffiche le panier
        return $this->redirectToRoute('app_cart_index');    
    }
}