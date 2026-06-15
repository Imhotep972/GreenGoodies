<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_USER')]
#[Route('/commande',name: 'app_order_')]
final class OrderController extends AbstractController
{
    public function __construct(private ProductRepository $productRepository, private OrderRepository $orderRepository, private EntityManagerInterface $entityManager, )
    {
    }

    #[route('/create',name:'create')]
    public function validate(SessionInterface $session,)
    {
        // on recupere le panier depuis la session
        $cart = $session->get('cart');

        if (!empty($cart))
        {
            // on cree une nouvelle commande
            $order = new Order();

            // on lui assigne l'utilisateur courant
            $order->setUser($this->getUser());

            // reference commande 'FA<YYYY><0number>' max 9999 factur par an
            $motif = "FA".date("Y");
            $newOrderReference = $this->orderRepository->getNewReference($motif);
            
            if (!empty($newOrderReference))
            {
                $order->setReference($newOrderReference);
                $totalAmount = 0;

                foreach($cart as $id => $quantity)
                {   // on boucle sur le contenu du panier , haque entree correspond à une ligne de commande
                    $product = $this->productRepository->find($id);
                    $orderLine = new OrderLine();
                    $orderLine->setQuantity($quantity);
                    $orderLine->setProduct($product);
                    $orderLine->setPrice($product->getPrice());

                    // on ajoute le total de la ligne au montant total de la commande
                    $totalAmount += $quantity* $product->getPrice();

                    // on ajoute la ligne de commande a la commande
                    $order->addOrderLine($orderLine);
                }
                // on sauvegarde la commande et les lignes de commande dans la base de donnees
                $order->setAmount($totalAmount);
                $this->entityManager->persist($order);
                $this->entityManager->flush();

                // on efface le panier
                $session ->remove('cart');

                // on affiche la page account avec la nouvelle commande
                return $this->redirectToRoute('app_account_index'); 
            }
            else
                $this->addFlash('cart', "Erreur : Le panier est vide");
        }
        else
            $this->addFlash('cart', "Erreur : Le panier est vide");

        // erreurs on reaffiche le panier
        return $this->redirectToRoute('app_cart_index');    
    }

}
