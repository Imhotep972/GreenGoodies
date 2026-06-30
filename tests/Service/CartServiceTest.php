<?php

namespace   App\Tests\Service;

use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

#[AllowMockObjectsWithoutExpectations]
class CartServiceTest extends TestCase
{

    /*
        addQuantity(Product $product): array
        removeQuantity(Product $product): array
        deleteProduct(Product $product): array
        emptyCart(): array
        getTotalCart(): int
        saveCart(array $cart): void
        generateOrder() : array
        getNewReference(String $motif) : string
    */
    private $entityManager;
    private $service;
    private $requestStack;
    private $security;
    private $orderRepository;
    private $productRepository;
    private $session;


    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);    // Mock EntityManager pour simuler la methode flush
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->session = $this->createMock(SessionInterface::class);
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->productRepository = $this->createMock(ProductRepository::class);

        $this->requestStack->method('getSession')->willreturn($this->session);
        $this->service = new CartService($this->security,  $this->entityManager,  $this->requestStack, $this->orderRepository,  $this->productRepository);    
    }

    private function createProduct(int $id, string $name="Produit",int $price = 1000,string $photo = 'Produit_1.webp'): Product
    {
        $product = new Product();
        $product->setPrice($price);
        $product->setName($name);
        $product->setPhoto($photo);

        $class = new \ReflectionClass(($product));
        $property = $class->getProperty('id');
        //$property->setAccessible(true);
        $property->setValue($product, $id);

        return $product;
    }

    // addQuantity(Product $product): array
    public function testAddQuantityNewCart()
    {
        $product = $this->createProduct(1,"Nécessaire, déodorant Bio",850,'Produit_1.webp');

        $id = $product->getId();
        $price = $product->getPrice();
        $this->session->method('get')->willReturn([]);                  // panier vide
        $this->session->expects($this->once())->method('set');          // sauvegarde du panier en session

        $result = $this->service->addQuantity($product);

        $status = $result['statut'];
        $message = $result['message'];
        $cart = $result['cart'];

        $this->assertSame('success',$status,'AddQuantity New Cart - Status Success');
        $this->assertSame('Panier : Le produit a été ajouté',$message,'AddQuantity New Cart - Message Success');

        $this->assertEquals(1,$cart[$id]['id'],'AddQuantity New Cart - id = 1');
        $this->assertEquals('Nécessaire, déodorant Bio',$cart[$id]['name'],'AddQuantity New Cart - name = Nécessaire, déodorant Bio');
        $this->assertEquals(850,$cart[$id]['price'],'AddQuantity New Cart price = 850');
        $this->assertEquals('Produit_1.webp',$cart[$id]['photo'],'AddQuantity New Cart - photo = Produit_1.webp');
        $this->assertEquals(1,$cart[$id]['quantity'],'AddQuantity New Cart - quantity = 1');
        $this->assertEquals(850,$cart[$id]['total'],'AddQuantity New Cart - total = 850');
    }

    public function testAddQuantityExistingProduct()
    {
        $product = $this->createProduct(1,"Nécessaire, déodorant Bio",850,'Produit_1.webp');
        $id = $product->getId();

        $this->session->method('get')->willReturn([
            1 => [
                "id" => 1,
                "name" => "Nécessaire, déodorant Bio",
                "price" => 850,
                "photo" => "produit.webp",
                "quantity" => 1,
                "total" => 850
            ]
        ]);               
        $this->session->expects($this->once())->method('set');         

        $result = $this->service->addQuantity($product);

        $status = $result['statut'];
        $message = $result['message'];
        $cart = $result['cart'];

        $this->assertSame('success',$status,'AddQuantity Existing Product - Status Success');
        $this->assertSame('Panier : La quantité du produit a été modifiée',$message,'AddQuantity Existing Product - Message Success');

        $this->assertEquals(2,$cart[$id]['quantity'],'AddQuantity Existing Product - quantity = 2');
        $this->assertEquals(1700,$cart[$id]['total'],'AddQuantity Existing Product - total = 1700');
    }

    public function testAddQuantityExistingCartNewProduct()
    {
        $product = $this->createProduct(2,"Kit couvert en bois",1230,'Produit_2.webp');

        $id = $product->getId();

        $this->session->method('get')->willReturn([
            1 => [
                "id" => 1,
                "name" => "Nécessaire, déodorant Bio",
                "price" => 850,
                "photo" => "produit_1.webp",
                "quantity" => 2,
                "total" => 1700
            ]
        ]);               
        $this->session->expects($this->once())->method('set');          // sauvegarde du panier en session

        $result = $this->service->addQuantity($product);

        $status = $result['statut'];
        $message = $result['message'];
        $cart = $result['cart'];

        $this->assertSame('success',$status,'AddQuantity Existing Cart New Product - Status Success');
        $this->assertSame('Panier : Le produit a été ajouté',$message,'AddQuantity Existing Cart New Product - Message Success');

        $this->assertEquals(2,$cart[$id]['id'],'AddQuantity Existing Cart New Product - id = 2');
        $this->assertEquals('Kit couvert en bois',$cart[$id]['name'],'AddQuantity Existing Cart New Product - name = Nécessaire, déodorant Bio');
        $this->assertEquals(1230,$cart[$id]['price'],'AddQuantity Existing Cart New Product - price = 850');
        $this->assertEquals('Produit_2.webp',$cart[$id]['photo'],'AddQuantity Existing Cart New Product - photo = Produit_2.webp');
        $this->assertEquals(1,$cart[$id]['quantity'],'AddQuantity Existing Cart New Product - quantity = 1');
        $this->assertEquals(1230,$cart[$id]['total'],'AddQuantity Existing Cart New Product - total = 850');
    }

    public function testAddQuantityExistingCartEmptyProduct()
    {
        $this->session->method('get')->willReturn([
            1 => [
                "id" => 1,
                "name" => "Nécessaire, déodorant Bio",
                "price" => 850,
                "photo" => "produit_1.webp",
                "quantity" => 2,
                "total" => 1700
            ]
        ]);               
        $this->session->expects($this->never())->method('set');

        $result = $this->service->addQuantity(null);

        $status = $result['statut'];
        $message = $result['message'];

        $this->assertSame('danger',$status,'AddQuantity Existing Cart Empty Product - Status Error');
        $this->assertSame('Panier : Le produit n\'existe pas',$message,'AddQuantity Existing Cart Empty Product - Message Error');
    }

    public function testAddQuantityError()
    {

        $product = $this->createProduct(2,"Kit couvert en bois",1230,'Produit_2.webp');

        $id = $product->getId();

        $this->session->method('get')->willReturn([
            1 => [
                "id" => 1,
                "name" => "Nécessaire, déodorant Bio",
                "price" => 850,
                "photo" => "produit_1.webp",
                "quantity" => 2,
                "total" => 1700
            ]
        ]); 

        $this->session->expects($this->once())->method('set')
                                              ->willThrowException(new \Exception("Erreur volontaire"));         
        $result = $this->service->addQuantity($product);

        $status = $result['statut'];
        $message = $result['message'];

        $this->assertSame('danger',$status,'AddQuantity Error - Status Error');
        $this->assertSame('Panier : Un problème est survenu lors de l\'ajout/modification du produit',$message,'AddQuantity Error - Message Error');
    }

    public function testRemoveQuantityEmptyCart()
    {
        $product = $this->createProduct(2,"Kit couvert en bois",1230,'Produit_2.webp');

        $this->session->method('get')->willReturn([]);
        $this->session->expects($this->never())->method('set');

        $result = $this->service->removeQuantity($product);
        $status = $result['statut'];
        $message = $result['message'];

        $this->assertSame('danger',$status,'RemoveQuantity Empty Cart - Status Error');
        $this->assertSame('Panier : Le panier est vide',$message,'RemoveQuantity Empty Cart - Message Error');
    }

    public function testRemoveQuantityExistingCartEmptyProduct()
    {
        $this->session->method('get')->willReturn([
            1 => [
                "id" => 1,
                "name" => "Nécessaire, déodorant Bio",
                "price" => 850,
                "photo" => "produit_1.webp",
                "quantity" => 2,
                "total" => 1700
            ]
        ]);               
        $this->session->expects($this->never())->method('set');

        $result = $this->service->removeQuantity(null);
        $status = $result['statut'];
        $message = $result['message'];

        $this->assertSame('danger',$status,'RemoveQuantity Existing Cart Empty Product - Status Error');
        $this->assertSame('Panier : Le produit n\'existe pas',$message,'RemoveQuantity Existing Cart Empty Product - Message Error');

    }

    public function testRemoveQuantityExistingCartExistingProduct()
    {
        $product = $this->createProduct(1,"Nécessaire, déodorant Bio",850,'Produit_1.webp');
        $id = $product->getId();

        $this->session->method('get')->willReturn([
            1 => [
                "id" => 1,
                "name" => "Nécessaire, déodorant Bio",
                "price" => 850,
                "photo" => "produit_1.webp",
                "quantity" => 2,
                "total" => 1700
            ]
        ]);  
        $this->session->expects($this->once())->method('set');

        $result = $this->service->removeQuantity($product);
        $status = $result['statut'];
        $message = $result['message'];
        $cart = $result['cart'];

        $this->assertSame('success',$status,'RemoveQuantity Existing Cart Existing Product - Status Success');
        $this->assertSame('Panier : La quantité du produit a été modifiée',$message,'RemoveQuantity Existing Cart Existing Product - Message Succes');

        $this->assertEquals(1,$cart[$id]['id'],'RemoveQuantity Existing Cart Existing Product - id = 1');
        $this->assertEquals(1,$cart[$id]['quantity'],'RemoveQuantity Existing Cart Existing Product - quantity = 2 - 1');
        $this->assertEquals(850,$cart[$id]['total'],'RemoveQuantity Existing Cart Existing Product - total = 850 x 1');
    }
   
    public function testRemoveQuantityExistingCartOneProductOnly()
    {
        $product = $this->createProduct(1,"Nécessaire, déodorant Bio",850,'Produit_1.webp');

        $this->session->method('get')->willReturn([
            1 => [
                "id" => 1,
                "name" => "Nécessaire, déodorant Bio",
                "price" => 850,
                "photo" => "produit_1.webp",
                "quantity" => 1,
                "total" => 850
            ]
        ]);  
        $this->session->expects($this->once())->method('set');

        $result = $this->service->removeQuantity($product);
        $status = $result['statut'];
        $message = $result['message'];
        $cart = $result['cart'];

        $this->assertSame('success',$status,'RemoveQuantity Existing Cart One Product Only - Status Success');
        $this->assertSame('Panier : Le panier est vide',$message,'RemoveQuantity Existing Cart One Product Only - Message Succes');

        $this->assertEmpty($cart,'RemoveQuantity Existing Cart One Product Only - Delete Cart');
    }

    public function testRemoveQuantityExistingCartOneProductAndOthers()
    {
        $product = $this->createProduct(1,"Nécessaire, déodorant Bio",850,'Produit_1.webp');
        $id = $product->getId();

        $this->session->method('get')->willReturn([
            1 => [
                "id" => 1,
                "name" => "Nécessaire, déodorant Bio",
                "price" => 850,
                "photo" => "produit_1.webp",
                "quantity" => 1,
                "total" => 1700
            ],
            2 => [
                "id" => 1,
                "name" => "Kit couvert en bois",
                "price" => 1230,
                "photo" => "produit_2.webp",
                "quantity" => 1,
                "total" => 1230
            ]        
        ]);  
        $this->session->expects($this->once())->method('set');

        $result = $this->service->removeQuantity($product);
        $status = $result['statut'];
        $message = $result['message'];
        $cart = $result['cart'];

        $this->assertSame('success',$status,'RemoveQuantity Existing Cart One Product And Others - Status Success');
        $this->assertSame('Panier : Le produit a été supprimé',$message,'RemoveQuantity Existing Cart One Product And Others - Message Success');

        $this->assertEmpty($cart[$id],'RemoveQuantity Existing Cart One Product And Others - Delete Product In Cart');
    }

    public function testRemoveQuantityExistingCartNotExistingProductInCart()
    {
        $product = $this->createProduct(1,"Nécessaire, déodorant Bio",850,'Produit_1.webp');
        $id = $product->getId();

        $this->session->method('get')->willReturn([
            2 => [
                "id" => 1,
                "name" => "Kit couvert en bois",
                "price" => 1230,
                "photo" => "produit_2.webp",
                "quantity" => 1,
                "total" => 1230
            ]        
        ]);  
        $this->session->expects($this->never())->method('set');

        $result = $this->service->removeQuantity($product);
        $status = $result['statut'];
        $message = $result['message'];

        $this->assertSame('danger',$status,'RemoveQuantity Existing Cart Not Existing Product In Cart - Status Error');
        $this->assertSame('Panier : Le produit n\'est pas dans le panier',$message,'RemoveQuantity Existing Cart Not Existing Product In Cart - Message Error');
    }

    public function testRemoveQuantityError()
    {
        $product = $this->createProduct(1,"Kit couvert en bois",1230,'Produit_2.webp');

        $this->session->method('get')->willReturn([
            1 => [
                "id" => 1,
                "name" => "Nécessaire, déodorant Bio",
                "price" => 850,
                "photo" => "produit_1.webp",
                "quantity" => 2,
                "total" => 1700
            ]
        ]);  

        $this->session->expects($this->once())->method('set')
                                              ->willThrowException(new \Exception("Erreur volontaire"));         
    
        $result = $this->service->removeQuantity($product);

        $status = $result['statut'];
        $message = $result['message'];
        $this->assertSame('danger',$status,'RemoveQuantity Error - Status Error');
        $this->assertSame('Panier : Un problème est survenu lors de la suppression/modification du produit',$message,'RemoveQuantity Error - Message Error');
    }

    public function testDeleteOneProductOnly()
    {
        $product = $this->createProduct(1,"Kit couvert en bois",1230,'Produit_2.webp');

        $this->session->method('get')->willReturn([
            1 => [
                "id" => 1,
                "name" => "Nécessaire, déodorant Bio",
                "price" => 850,
                "photo" => "produit_1.webp",
                "quantity" => 2,
                "total" => 1700
            ]
        ]);  
        $this->session->expects($this->once())->method('set');

        $result = $this->service->deleteProduct($product);
        $status = $result['statut'];
        $message = $result['message'];
        $cart = $result['cart'];

        $this->assertSame('success',$status,'Delete One Product Only - Status Success');
        $this->assertSame('Panier : Le produit a bien été supprimé',$message,'Delete One Product Only - Message Success');

        $this->assertEmpty($cart,'Delete One Product Only - Delete Cart');
    }

    public function testDeleteOneProductWithOthers()
    {
        $product = $this->createProduct(1,"Kit couvert en bois",1230,'Produit_2.webp');
        $id = $product->getId();

        $this->session->method('get')->willReturn([
            1 => [
                "id" => 1,
                "name" => "Nécessaire, déodorant Bio",
                "price" => 850,
                "photo" => "produit_1.webp",
                "quantity" => 2,
                "total" => 1700
            ],
            2 => [
                "id" => 1,
                "name" => "Kit couvert en bois",
                "price" => 1230,
                "photo" => "produit_2.webp",
                "quantity" => 1,
                "total" => 1230
            ]    
        ]);  
        $this->session->expects($this->once())->method('set');

        $result = $this->service->deleteProduct($product);
        $status = $result['statut'];
        $message = $result['message'];
        $cart = $result['cart'];

        $this->assertSame('success',$status,'Delete One Product Only And Others - Status Success');
        $this->assertSame('Panier : Le produit a bien été supprimé',$message,'Delete One Product Only With Others - Message Success');

        $this->assertEmpty($cart[$id],'Delete One Product Only With Others - Delete Product');
    }

    public function testDeleteNotExistingProductInCart()
    {
        $product = $this->createProduct(2,"Kit couvert en bois",1230,'Produit_2.webp');
        $id = $product->getId();

        $this->session->method('get')->willReturn([
            1 => [
                "id" => 1,
                "name" => "Nécessaire, déodorant Bio",
                "price" => 850,
                "photo" => "produit_1.webp",
                "quantity" => 2,
                "total" => 1700
            ],
        ]);  
        $this->session->expects($this->never())->method('set');

        $result = $this->service->deleteProduct($product);

        $status = $result['statut'];
        $message = $result['message'];

        $this->assertSame('danger',$status,'Delete Not Existing Product In Cart - Status Error');
        $this->assertSame("Panier : Le produit n'est pas dans le panier",$message,'Delete Not Existing Product In Cart - Message Error');
    }

    public function testDeleteEmptyProduct()
    {
        $this->session->method('get')->willReturn([
            1 => [
                "id" => 1,
                "name" => "Nécessaire, déodorant Bio",
                "price" => 850,
                "photo" => "produit_1.webp",
                "quantity" => 2,
                "total" => 1700
            ],
        ]);  
        $this->session->expects($this->never())->method('set');

        $result = $this->service->deleteProduct(null);

        $status = $result['statut'];
        $message = $result['message'];

        $this->assertSame('danger',$status,'Delete Empty Product - Status Error');
        $this->assertSame("Panier : Le produit n'existe pas",$message,'Delete Empty Product - Message Error');
    }

    public function testDeleteProductError()
    {
        $product = $this->createProduct(1,"Kit couvert en bois",1230,'Produit_2.webp');

        $this->session->method('get')->willReturn([
            1 => [
                "id" => 1,
                "name" => "Nécessaire, déodorant Bio",
                "price" => 850,
                "photo" => "produit_1.webp",
                "quantity" => 2,
                "total" => 1700
            ]
        ]);  

        $this->session->expects($this->once())->method('set')
                                              ->willThrowException(new \Exception("Erreur volontaire"));         
    
        $result = $this->service->deleteProduct($product);

        $status = $result['statut'];
        $message = $result['message'];
        $this->assertSame('danger',$status,'Delete Product Error - Status Error');
        $this->assertSame('Panier : Un problème est survenu lors de la suppression du produit',$message,'Delete Product Error - Message Error');
    }

    public function testEmptyCart()
    {
       $this->session->method('get')->willReturn([
            1 => [
                "id" => 1,
                "name" => "Nécessaire, déodorant Bio",
                "price" => 850,
                "photo" => "produit_1.webp",
                "quantity" => 2,
                "total" => 1700
            ]
        ]);  

        $this->session->expects($this->once())->method('remove');

        $result = $this->service->emptyCart();
        $status = $result['statut'];
        $message = $result['message'];
        $cart = $result['cart'];

        $this->assertSame('success',$status,'Empty Cart - Status Success');
        $this->assertSame('Panier : Le panier a été supprimé',$message,'Empty Cart - Message Success');
        $this->assertEmpty($cart,'Empty Cart - Delete Cart');
    }

    public function testEmptyCartEmpty()
    {
       $this->session->method('get')->willReturn([]);  

        $this->session->expects($this->once())->method('remove');

        $result = $this->service->emptyCart();
        $status = $result['statut'];
        $message = $result['message'];
        $cart = $result['cart'];

        $this->assertSame('success',$status,'Empty Cart Empty - Status Success');
        $this->assertSame('Panier : Le panier a été supprimé',$message,'Empty Cart Empty - Message Success');
        $this->assertEmpty($cart,'Empty Cart Empty - Delete Cart');
    }

    public function testEmptyCartError()
    {
       $this->session->method('get')->willReturn([
            1 => [
                "id" => 1,
                "name" => "Nécessaire, déodorant Bio",
                "price" => 850,
                "photo" => "produit_1.webp",
                "quantity" => 2,
                "total" => 1700
            ]
        ]);  

        $this->session->expects($this->once())->method('remove')
                                              ->willThrowException(new \Exception("Erreur volontaire")); 

        $result = $this->service->emptyCart();
        $status = $result['statut'];
        $message = $result['message'];
        $cart = $result['cart'];

        $this->assertSame('danger',$status,'Empty Cart - Status Error');
        $this->assertSame('Panier : Un problème est survenu lors de la suppression du panier',$message,'Empty Cart - Message Error');
    }

    public function testGetNewReferenceFirst()
    {
        $this->orderRepository
            ->method('getNewReference')
            ->willReturn(null);
        $result = $this->service->getNewReference("FA2026");

        $this->assertSame("FA20260001", $result, "Get New Reference First");
    }

    public function testGetNewReference()
    {
        $this->orderRepository
            ->method('getNewReference')
            ->willReturn('FA20260052');
        $result = $this->service->getNewReference("FA2026");

        $this->assertSame("FA20260053", $result, "Get New Reference");
    }

    
}