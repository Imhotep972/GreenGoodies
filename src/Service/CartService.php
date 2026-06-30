<?php
// src/Service/UserTools.php
namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $session = null;

    public function __construct(private  Security $security,private EntityManagerInterface $entityManager, RequestStack  $requestStack, private OrderRepository $orderRepository,private ProductRepository $productRepository, )
    {
        $this->session = $requestStack->getSession();
    }

    public function addQuantity(?Product $product): array
    {
        try
        {
            $cart = $this->session->get('cart',[]);
            if (empty($product))
                return [
                    'statut' => 'danger',
                    'message' => 'Panier : Le produit n\'existe pas',   
                    'cart' => $cart,
                    ];

            // on recupere le panier de la session si il existe,  sinon on le cree ([])
            // on recupeère l'id du produit
            $id = $product->getId();
            if (empty($cart[$id]))
            {   // on ajoute le produit dans le panier
                $cart[$id] = [
                    'id'       =>   $id,
                    'name'     =>   $product->getName(),
                    'price'    =>   $product->getPrice(),
                    'photo'    =>   $product->getPhoto(),
                    'quantity' =>   1,
                ];
                $message = 'Panier : Le produit a été ajouté';
            }
            else        // le produit est deja dans le panier
            {
                $cart[$id]['quantity']++;
                $message = 'Panier : La quantité du produit a été modifiée';
            }

            // on met à jour le sous total pour le produit
            $cart[$id]['total'] = $cart[$id]['price']*$cart[$id]['quantity'];

            // on met à jour le panier dans la session
            $this->saveCart($cart);

            return [
                'statut' => 'success',
                'message' => $message,
                'cart' => $cart
            ];
        }
        catch (\Throwable $e) 
        {
            return [
                'statut' => 'danger',
                'message' => 'Panier : Un problème est survenu lors de l\'ajout/modification du produit',
                'cart' => $cart,
            ];
        }
    }

    public function removeQuantity(?Product $product): array
    {
        try
        {
            // on recupere le panier de la session 
            $cart = $this->session->get('cart',[]);
            if (empty($cart))
                return [
                    'statut' => 'danger',
                    'message' => 'Panier : Le panier est vide',   
                    'cart' => $cart
                    ];        

            if ( empty($product) || empty($cart[$product->getId()]) ) 
                // le produit n'existe pas ou n'existe pas dans le panier
                return [
                    'statut' => 'danger',
                    'message' => ( empty($product) )? "Panier : Le produit n'existe pas" : "Panier : Le produit n'est pas dans le panier",   
                    'cart' => $cart,
                    ];        

            // on recupeère l'id du produit
            $id = $product->getId();
            switch($cart[$id]['quantity'])
            {
                case 1 :    unset($cart[$id]);
                            $message = (empty($cart))? 'Panier : Le panier est vide' : 'Panier : Le produit a été supprimé';
                            break;
                default :   $cart[$id]['quantity']--;
                            $cart[$id]['total'] = $cart[$id]['price']*$cart[$id]['quantity'];
                            $message = 'Panier : La quantité du produit a été modifiée';
                            break;
            }

             // on met à jour le panier dans la session
            $this->saveCart($cart);

            return [
                'statut' => 'success',
                'message' => $message,                
                'cart' => $cart
            ];

        }
        catch (\Throwable $e) 
        {
            return [
                'statut' => 'danger',
                'message' => 'Panier : Un problème est survenu lors de la suppression/modification du produit',
                'cart' => $cart,
            ];
        }
    }

    public function deleteProduct(?Product $product): array
    {
        try
        {
            // on recupere le panier de la session 
            $cart = $this->session->get('cart',[]);
            
            if (empty($product) || empty($cart[$product->getId()])) 
                // le produit n'existe pas ou n'existe pas dans le panier
                return [
                    'statut' => 'danger',
                    'message' => (empty($product))? 'Panier : Le produit n\'existe pas' : "Panier : Le produit n'est pas dans le panier",    
                    'cart' => $cart,
                    ];

            // on recupere le panier de la session 
            $cart = $this->session->get('cart',[]);

            // on supprime le produit du panier
            unset($cart[$product->getId()]);

            // on met à jour le panier dans la session
            $this->saveCart($cart);

            return [
                'statut' => 'success',
                'message' => 'Panier : Le produit a bien été supprimé',
                'cart' => $cart,
            ];
        }
        catch (\Throwable $e) 
        {
            return [
                'statut' => 'danger',
                'message' => 'Panier : Un problème est survenu lors de la suppression du produit',
                'cart' => $cart,
            ];
        }
    }

    public function emptyCart(): array
    {
        // on recupere le panier de la session 
        $cart = $this->session->get('cart',[]);

        try
        {
            $this->session->remove('cart');
            return [
                'statut' => 'success',
                'message' => 'Panier : Le panier a été supprimé',
                'cart' => [],
            ];
        }
        catch (\Throwable $e) 
        {
            return [
                'statut' => 'danger',
                'message' => 'Panier : Un problème est survenu lors de la suppression du panier',
                'cart' => $cart,

            ];
        }
    }
    
    public function getTotalCart(): int
    {
        $cart = $this->session->get('cart');

        if (empty($cart))
            return 0;

        $totalPanier = 0;
        foreach ($cart as $product)
        {
            $totalPanier += $product['total'];
        }

        return $totalPanier;
    }

    public function saveCart(array $cart): void
    {
        $this->session->set('cart',$cart);
    }

    public function generateOrder() : array
    {
        try
        {
            $cart = $this->session->get('cart',[]);

            if (empty($cart))
                return [
                    'statut' => 'danger',
                    'message' => 'Commande : Erreur pendant la génération de la commande, le panier est vide',
                ];

            // nouvelle instance de Order
            $order = New Order();

            // reference commande 'FA<YYYY><0number>' max 9999 factur par an
            $newOrderReference = $this->getNewReference("FA".date("Y"));
            $order->setReference($newOrderReference);    

            // total de la facture
            $totalAmount = 0;

            foreach($cart as $id => $item)
            {   // on boucle sur le contenu du panier , chaque entree correspond à une ligne de commande
                $product = $this->productRepository->find($id);
                $orderLine = new OrderLine();
                $orderLine->setQuantity($item['quantity']);
                $orderLine->setProduct($product);
                $orderLine->setPrice($item['price']);

                // on ajoute le total de la ligne au montant total de la commande
                $totalAmount += $item['total'];

                // on ajoute la ligne de commande a la commande
                $order->addOrderLine($orderLine);
            }
            // on sauvegarde la commande et les lignes de commande dans la base de donnees
            $order->setAmount($totalAmount);
            $order->setUser($this->security->getUser());
            $this->entityManager->persist($order);
            $this->entityManager->flush();

            // on efface le panier
            $this->emptyCart();

            // on affiche la page account avec la nouvelle commande
            return [
                'statut' => 'success',
                'message' => 'Commande : La commande a été générée sans erreur'
            ]; 
        }
        catch (\Throwable $e) 
        {
            return [
                'statut' => 'danger',
                'message' => 'Commande : Un problème est survenu lors de la génération de la commande'
            ];
        }
    }

    public function getNewReference($motif) : string
    {
        $lastReference = $this->orderRepository->getNewReference($motif);
        $newReference =($lastReference === null) ? $motif.\sprintf("%04d",\intval(1)) : $motif.\sprintf("%04d",\intval(str_replace($motif,'',$lastReference),10)+1);

        return $newReference;
    }
}
