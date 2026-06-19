<?php
// src/Service/UserTools.php
namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderLine;
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
    public function addQuantity($product): array
    {
        try
        {
            if (empty($product))
                return [
                    'statut' => 'danger',
                    'message' => 'Le produit n\'existe pas',   
                    ];

            // on recupere le panier de la session si il existe,  sinon on le cree ([])
            $cart = $this->session->get('cart',[]);
            // on recupeère l'id du produit
            $id = $product->getId();
            if (empty($cart[$id]))
            {   // on ajoute le produit dans le panier
                $cart[$id]['id'] = $id;
                $cart[$id]['name'] = $product->getName();
                $cart[$id]['price'] = $product->getPrice();
                $cart[$id]['photo'] = $product->getPhoto();
                $cart[$id]['quantity'] = 1;
            }
            else        // le produit est deja dans le panier
                $cart[$id]['quantity']++;

            // on met à jour le sous total pour le produit
            $cart[$id]['total'] = $cart[$id]['price']*$cart[$id]['quantity'];

            // on met à jour le panier dans la session
            $this->saveCart($cart);

            return [
                'statut' => 'success',
                'message' => 'La quantité du produit a bien été modifiée',
            ];
        }
        catch (\Throwable $e) 
        {
            return [
                'statut' => 'danger',
                'message' => 'Un problème est survenu lors de la modification de la quantité.'
            ];
        }
    }

    public function removeQuantity($product): array
    {
        try
        {
            // on recupere le panier de la session 
            $cart = $this->session->get('cart',[]);
            if (empty($product) || empty($cart[$product->getId()])) 
                // le produit n'existe pas ou n'existe pas dans le panier
                return [
                    'statut' => 'danger',
                    'message' => 'Le produit n\'existe pas dans le panier',   
                    ];

            // on recupeère l'id du produit
            $id = $product->getId();
            switch($cart[$id]['quantity'])
            {
                case 1 :    unset($cart[$id]);
                            $message = 'Le produit a bien été supprimé du panier';
                            break;
                default :   $cart[$id]['quantity']--;
                            $cart[$id]['total'] = $cart[$id]['price']*$cart[$id]['quantity'];
                            $message = 'La quantité du produit a bien été modifiée';
                            break;
            }

             // on met à jour le panier dans la session
            $this->saveCart($cart);

            return [
                'statut' => 'success',
                'message' => $message,
            ];
        }
        catch (\Throwable $e) 
        {
            return [
                'statut' => 'danger',
                'message' => 'Un problème est survenu lors de la modification de la quantité.'
            ];
        }
    }

    public function deleteProduct($product): array
    {
        try
        {
            // on recupere le panier de la session 
            $cart = $this->session->get('cart',[]);
            
            if (empty($product) || empty($cart[$product->getId()])) 
                // le produit n'existe pas ou n'existe pas dans le panier
                return [
                    'statut' => 'danger',
                    'message' => 'Le produit n\'existe pas dans le panier',   
                    ];

            // on recupere le panier de la session 
            $cart = $this->session->get('cart',[]);

            // on supprime le produit du panier
            unset($cart[$product->getId()]);

            // on met à jour le panier dans la session
            $this->saveCart($cart);

            return [
                'statut' => 'success',
                'message' => 'Le produit a bien été supprimé du panier',
            ];
        }
        catch (\Throwable $e) 
        {
            return [
                'statut' => 'danger',
                'message' => 'Un problème est survenu lors de la suppression du produit du panier'
            ];
        }
    }
    public function emptyCart(): array
    {
        try
        {
            $this->session->remove('cart');
            return [
                'statut' => 'success',
                'message' => 'Le panier a été vidé',
            ];
        }
        catch (\Throwable $e) 
        {
            return [
                'statut' => 'danger',
                'message' => 'Un problème est survenu lors de la suppression du panier'
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
                    'message' => 'Erreur pendant la génération de la commande, le panier est vide',
                ];

            // nouvelle instance de Order
            $order = New Order();

            // reference commande 'FA<YYYY><0number>' max 9999 factur par an
            $newOrderReference = $this->getNewReference();
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
                'message' => 'La commande a été générée sans erreur'
            ]; 
        }
        catch (\Throwable $e) 
        {
            return [
                'statut' => 'danger',
                'message' => 'Un problème est survenu lors de la génération de la commande'
            ];
        }
    }

    public function getNewReference() : string
    {
        $motif = "FA".date("Y");
        $lastReference = $this->orderRepository->getNewReference($motif);

        $newReference = "FA".date("Y").\sprintf("%04d",\intval(str_replace($motif,'',$lastReference),10)+1);

        return $newReference;
    }
}
