<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\OrderLine;
use App\Entity\Order;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_USER')]
#[Route('/commande',name: 'app_gg_order_')]
final class OrderController extends AbstractController
{
    public function __construct(private ProductRepository $productRepository, private EntityManagerInterface $entityManager, )
    {
    }

    #[route('/create',name:'create')]
    public function validate(SessionInterface $session,)
    {
        // on recupere le panier depuis la session
        $panier = $session->get('panier');

        if (!empty($panier))
        {
            // on cree une nouvelle commande
            $order = new Order();
            // on lui assigne l'utilisateur courant
            $order->setUser($this->getUser());
            
            // on boucle sur le panier 
            // chaque entree du panier correspond a une ligne de commande
            foreach($panier as $id => $quantite)
            {
                $orderLine = new OrderLine();
                $orderLine->setQuantite($quantite);
                $orderLine->setProduct($this->productRepository->find($id));
            
                // on ajoute la ligne de commande a la commande
                $order->addOrderLine($orderLine);
            }
            // on sauvegarde dans la base de donnees
            $this->entityManager->persist($order);
            $this->entityManager->flush();

            // on efface le panier
            $session ->remove('panier');

            return $this->redirectToRoute('app_gg_account_index'); 
        }
        else
        {
            // pas de panier on reaffiche le panier
            return $this->redirectToRoute('app_gg_cart_index');    
        }
    }

}
